<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\CourseRepository;
use Repositories\StudentRepository;
use Repositories\AttendanceRepository;
use Services\AttendanceService;

final class AttendanceController extends Controller
{
    private CourseRepository $courses;
    private StudentRepository $students;
    private AttendanceRepository $attendance;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new CourseRepository();
        $this->students = new StudentRepository();
        $this->attendance = new AttendanceRepository();
    }

    public function index(): void
    {
        $role = auth_role();

        if ($role === 'student') {
            $student = $this->students->findByUserId(auth()['id']);
            $studentId = $student['id'] ?? 0;
            $myRecords = $studentId ? $this->attendance->forStudent($studentId) : [];

            $total = count($myRecords);
            $present = count(array_filter($myRecords, fn($r) => $r['status'] === 'present'));
            $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0.0;

            $this->view('attendance.index', [
                'isStudent' => true,
                'student' => $student,
                'records' => $myRecords,
                'totalClasses' => $total,
                'presentClasses' => $present,
                'percentage' => $pct,
                'title' => 'My Attendance Records',
            ]);
            return;
        }

        $this->authorize('admin', 'operator', 'faculty');
        $courseId = (int)$this->request->input('course_id', 0);
        $date = $this->request->input('date', date('Y-m-d'));

        $coursesList = $this->courses->all('name');
        $records = [];
        if ($courseId) {
            $records = $this->attendance->byCourseAndDate($courseId, $date);
            if (empty($records)) {
                $studentsList = $this->students->where('course_id', $courseId);
                $records = array_map(fn($s) => [
                    'student_id' => $s['id'],
                    'student_name' => $s['name'],
                    'admission_no' => $s['admission_no'],
                    'status' => 'present',
                ], $studentsList);
            }
        }

        $this->view('attendance.index', [
            'isStudent' => false,
            'courses' => $coursesList,
            'records' => $records,
            'courseId' => $courseId,
            'date' => $date,
            'title' => 'Attendance Management',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $courseId = (int)$this->request->input('course_id');
        $date = $this->request->input('date');
        $studentIds = $this->request->input('student_id', []);
        $statuses = $this->request->input('status', []);

        $recordsToMark = [];
        foreach ($studentIds as $i => $sid) {
            $recordsToMark[] = [
                'student_id' => (int)$sid,
                'status' => $statuses[$i] ?? 'present',
            ];
        }

        $count = (new AttendanceService())->markBulk($courseId, $date, $recordsToMark, auth()['id']);
        flash('success', "Attendance marked for {$count} students on {$date}.");
        redirect("/attendance?course_id={$courseId}&date={$date}");
    }
}
