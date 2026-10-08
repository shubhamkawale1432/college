<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\ExamRepository;
use Repositories\CourseRepository;
use Repositories\StudentRepository;
use Repositories\ResultRepository;
use Services\ResultService;

final class ResultController extends Controller
{
    private ExamRepository $exams;
    private CourseRepository $courses;
    private StudentRepository $students;
    private ResultRepository $results;

    public function __construct()
    {
        parent::__construct();
        $this->exams = new ExamRepository();
        $this->courses = new CourseRepository();
        $this->students = new StudentRepository();
        $this->results = new ResultRepository();
    }

    public function index(): void
    {
        $role = auth_role();
        $examId = (int)$this->request->input('exam_id', 0);
        $allExams = $this->exams->all('exam_date DESC');
        $allCourses = $this->courses->all('name');

        if ($role === 'student') {
            $student = $this->students->findByUserId(auth()['id']);
            $studentResults = $student ? $this->results->forStudent((int)$student['id']) : [];

            $this->view('results.index', [
                'isStudent' => true,
                'student' => $student,
                'results' => $studentResults,
                'exams' => $allExams,
                'courses' => $allCourses,
                'examId' => 0,
                'title' => 'My Examination Results & Transcript',
            ]);
            return;
        }

        $resultsList = $examId ? $this->results->forExam($examId) : [];

        $this->view('results.index', [
            'isStudent' => false,
            'exams' => $allExams,
            'courses' => $allCourses,
            'results' => $resultsList,
            'examId' => $examId,
            'title' => 'Exam Results & Grading',
        ]);
    }

    public function storeExam(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $data = $this->validate([
            'title' => 'required|min:3|max:150',
            'course_id' => 'required|numeric',
            'exam_date' => 'required|date',
        ]);

        $examId = $this->exams->create([
            'title' => trim($data['title']),
            'course_id' => (int)$data['course_id'],
            'exam_date' => $data['exam_date'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        flash('success', "Exam '{$data['title']}' scheduled successfully.");
        redirect("/results/create?exam_id={$examId}");
    }

    public function create(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $examId = (int)$this->request->input('exam_id');
        $exam = $this->exams->find($examId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect('/results');
        }

        $studentsList = $this->students->where('course_id', $exam['course_id']);

        $this->view('results.create', [
            'exam' => $exam,
            'students' => $studentsList,
            'title' => "Enter Results - {$exam['title']}",
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $examId = (int)$this->request->input('exam_id');
        $studentIds = $this->request->input('student_id', []);
        $marks = $this->request->input('marks_obtained', []);
        $maxMarks = (float)$this->request->input('max_marks', 100);

        $entries = [];
        foreach ($studentIds as $i => $sid) {
            if (!isset($marks[$i]) || $marks[$i] === '') {
                continue;
            }
            $entries[] = [
                'student_id' => (int)$sid,
                'marks_obtained' => (float)$marks[$i],
                'max_marks' => $maxMarks,
            ];
        }

        $count = (new ResultService())->publishBulk($examId, $entries, auth()['id']);
        flash('success', "Results published for {$count} students.");
        redirect("/results?exam_id={$examId}");
    }
}
