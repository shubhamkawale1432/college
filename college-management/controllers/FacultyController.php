<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Database;
use Repositories\FacultyRepository;
use Repositories\DepartmentRepository;
use Repositories\UserRepository;
use Repositories\ActivityLogRepository;
use Services\UploadService;

final class FacultyController extends Controller
{
    private FacultyRepository $faculty;
    private DepartmentRepository $departments;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->faculty = new FacultyRepository();
        $this->departments = new DepartmentRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator');
        $page = (int)$this->request->input('page', 1);
        $search = $this->request->input('q');
        $deptId = $this->request->input('department_id') ? (int)$this->request->input('department_id') : null;
        $trash = (bool)$this->request->input('trash', false);

        $pagination = $this->faculty->paginateFaculty($page, 15, $search, $deptId, $trash);
        $depts = $this->departments->all('name');

        $this->view('faculty.index', [
            'pagination' => $pagination,
            'departments' => $depts,
            'search' => $search,
            'currentDept' => $deptId,
            'trash' => $trash,
            'title' => 'Faculty & Academic Staff',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'department_id' => 'required|numeric',
            'designation' => 'required|max:100',
        ]);

        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $photoPath = UploadService::saveImage($_FILES['photo'], 'avatars');
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/faculty');
            }
        }

        Database::beginTransaction();
        try {
            $tempPassword = bin2hex(random_bytes(4));
            $users = new UserRepository();

            if ($users->findByEmail($data['email'], true)) {
                flash('error', 'A user with this email address already exists.');
                redirect('/faculty');
            }

            $userId = $users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
                'role' => 'faculty',
                'avatar' => $photoPath,
                'phone' => $this->request->input('phone'),
                'failed_attempts' => 0,
                'two_factor_enabled' => 0,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $id = $this->faculty->create([
                'user_id' => $userId,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $this->request->input('phone'),
                'photo' => $photoPath,
                'department_id' => (int)$data['department_id'],
                'designation' => $data['designation'],
                'qualification' => $this->request->input('qualification'),
                'joining_date' => $this->request->input('joining_date') ?: date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->logs->record(auth()['id'], 'faculty_created', 'faculty', $id, null, $data);
            Database::commit();

            flash('success', "Faculty added successfully! Temporary password: {$tempPassword}");
        } catch (\Throwable $e) {
            Database::rollBack();
            flash('error', 'Could not add faculty: ' . $e->getMessage());
        }

        redirect('/faculty');
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $fid = (int)$id;

        $this->faculty->softDelete($fid);
        $this->logs->record(auth()['id'], 'faculty_deleted', 'faculty', $fid, null, null);

        flash('success', 'Faculty member moved to trash.');
        redirect('/faculty');
    }

    public function restore(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $fid = (int)$id;

        $this->faculty->restore($fid);
        $this->logs->record(auth()['id'], 'faculty_restored', 'faculty', $fid, null, null);

        flash('success', 'Faculty member restored successfully.');
        redirect('/faculty?trash=1');
    }
}
