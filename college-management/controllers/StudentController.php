<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\StudentRepository;
use Repositories\DepartmentRepository;
use Repositories\CourseRepository;
use Repositories\AttendanceRepository;
use Repositories\ResultRepository;
use Repositories\FeeRepository;
use Services\StudentService;
use Services\UploadService;

final class StudentController extends Controller
{
    private StudentRepository $students;
    private DepartmentRepository $departments;
    private CourseRepository $courses;
    private StudentService $service;

    public function __construct()
    {
        parent::__construct();
        $this->students = new StudentRepository();
        $this->departments = new DepartmentRepository();
        $this->courses = new CourseRepository();
        $this->service = new StudentService();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator', 'faculty');

        $page = (int)$this->request->input('page', 1);
        $search = $this->request->input('q');
        $deptId = $this->request->input('department_id') ? (int)$this->request->input('department_id') : null;
        $courseId = $this->request->input('course_id') ? (int)$this->request->input('course_id') : null;
        $status = $this->request->input('status');
        $trash = (bool)$this->request->input('trash', false);

        $pagination = $this->students->paginateStudents($page, 15, $search, $deptId, $courseId, $status, $trash);
        $depts = $this->departments->all('name');
        $allCourses = $this->courses->all('name');

        $this->view('students.index', [
            'pagination' => $pagination,
            'departments' => $depts,
            'courses' => $allCourses,
            'search' => $search,
            'currentDept' => $deptId,
            'currentCourse' => $courseId,
            'currentStatus' => $status,
            'trash' => $trash,
            'title' => 'Student Directory',
        ]);
    }

    public function create(): void
    {
        $this->authorize('admin', 'operator');
        $this->view('students.create', [
            'departments' => $this->departments->all('name'),
            'courses' => $this->courses->all('name'),
            'title' => 'Enroll New Student',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'max:20',
            'gender' => 'required|in:male,female,other',
            'semester' => 'required|numeric',
        ]);

        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $photoPath = UploadService::saveImage($_FILES['photo'], 'students');
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/students/create');
            }
        }

        try {
            $result = $this->service->enroll($this->request->all(), auth()['id'], $photoPath);
            flash('success', "Student enrolled! Admission No: {$result['admission_no']} (Temp Password: {$result['temp_password']})");
        } catch (\Throwable $e) {
            flash('error', 'Enrollment failed: ' . $e->getMessage());
            redirect('/students/create');
        }

        redirect('/students');
    }

    public function show(string $id): void
    {
        $sid = (int)$id;
        $student = $this->students->findWithRelations($sid);
        if (!$student) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        // Students can only view their own record
        if (has_role('student') && (int)$student['user_id'] !== auth()['id']) {
            http_response_code(403);
            $this->view('errors.403');
            return;
        }

        $attendanceRepo = new AttendanceRepository();
        $resultRepo = new ResultRepository();
        $feeRepo = new FeeRepository();

        $attendanceRecords = $attendanceRepo->forStudent($sid);
        $examResults = $resultRepo->forStudent($sid);
        $feeInvoices = $feeRepo->forStudent($sid);

        $printView = $this->request->input('print');

        $this->view('students.show', [
            'student' => $student,
            'attendanceRecords' => $attendanceRecords,
            'examResults' => $examResults,
            'feeInvoices' => $feeInvoices,
            'printMode' => $printView, // 'id_card', 'report_card'
            'title' => "Student - {$student['name']}",
        ]);
    }

    public function edit(string $id): void
    {
        $this->authorize('admin', 'operator');
        $sid = (int)$id;
        $student = $this->students->findWithRelations($sid);
        if (!$student) {
            flash('error', 'Student not found.');
            redirect('/students');
        }

        $this->view('students.edit', [
            'student' => $student,
            'departments' => $this->departments->all('name'),
            'courses' => $this->courses->all('name'),
            'title' => "Edit Student - {$student['name']}",
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $sid = (int)$id;

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'gender' => 'required|in:male,female,other',
            'semester' => 'required|numeric',
        ]);

        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $photoPath = UploadService::saveImage($_FILES['photo'], 'students');
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect("/students/{$sid}/edit");
            }
        }

        $this->service->update($sid, $this->request->all(), auth()['id'], $photoPath);
        flash('success', "Student {$data['name']} details updated.");
        redirect("/students/{$sid}");
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $sid = (int)$id;

        $this->service->delete($sid, auth()['id']);
        flash('success', 'Student moved to trash.');
        redirect('/students');
    }

    public function restore(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $sid = (int)$id;

        $this->service->restore($sid, auth()['id']);
        flash('success', 'Student restored successfully.');
        redirect('/students?trash=1');
    }
}
