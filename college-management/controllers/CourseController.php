<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\CourseRepository;
use Repositories\DepartmentRepository;
use Repositories\ActivityLogRepository;

final class CourseController extends Controller
{
    private CourseRepository $courses;
    private DepartmentRepository $departments;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->courses = new CourseRepository();
        $this->departments = new DepartmentRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator', 'faculty', 'student');
        $trash = (bool)$this->request->input('trash', false);

        $coursesList = $this->courses->withDepartment($trash);
        $depts = $this->departments->all('name');

        $this->view('courses.index', [
            'courses' => $coursesList,
            'departments' => $depts,
            'trash' => $trash,
            'title' => 'Courses & Academic Programs',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'code' => 'required|min:2|max:20',
            'department_id' => 'required|numeric',
            'duration_years' => 'required|numeric',
        ]);

        $id = $this->courses->create([
            'name' => trim($data['name']),
            'code' => strtoupper(trim($data['code'])),
            'department_id' => (int)$data['department_id'],
            'duration_years' => (int)$data['duration_years'],
            'credits' => (int)($this->request->input('credits', 3)),
            'fee_per_year' => (float)($this->request->input('fee_per_year', 2500.00)),
            'description' => $this->request->input('description'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logs->record(auth()['id'], 'course_created', 'courses', $id, null, $data);
        flash('success', "Course '{$data['name']}' added.");
        redirect('/courses');
    }

    public function update(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $cid = (int)$id;

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'code' => 'required|min:2|max:20',
            'department_id' => 'required|numeric',
            'duration_years' => 'required|numeric',
        ]);

        $before = $this->courses->find($cid, true);
        $updateData = [
            'name' => trim($data['name']),
            'code' => strtoupper(trim($data['code'])),
            'department_id' => (int)$data['department_id'],
            'duration_years' => (int)$data['duration_years'],
            'credits' => (int)($this->request->input('credits', 3)),
            'fee_per_year' => (float)($this->request->input('fee_per_year', 2500.00)),
            'description' => $this->request->input('description'),
        ];

        $this->courses->update($cid, $updateData);
        $this->logs->record(auth()['id'], 'course_updated', 'courses', $cid, $before, $updateData);

        flash('success', "Course '{$data['name']}' updated.");
        redirect('/courses');
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $cid = (int)$id;

        $this->courses->softDelete($cid);
        $this->logs->record(auth()['id'], 'course_deleted', 'courses', $cid, null, null);

        flash('success', 'Course moved to trash.');
        redirect('/courses');
    }

    public function restore(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $cid = (int)$id;

        $this->courses->restore($cid);
        $this->logs->record(auth()['id'], 'course_restored', 'courses', $cid, null, null);

        flash('success', 'Course restored successfully.');
        redirect('/courses?trash=1');
    }
}
