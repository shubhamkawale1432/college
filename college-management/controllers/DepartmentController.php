<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\DepartmentRepository;
use Repositories\ActivityLogRepository;

final class DepartmentController extends Controller
{
    private DepartmentRepository $departments;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->departments = new DepartmentRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $trash = (bool)$this->request->input('trash', false);
        $depts = $this->departments->allWithStats($trash);

        $this->view('departments.index', [
            'departments' => $depts,
            'trash' => $trash,
            'title' => 'Departments Directory',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'code' => 'required|min:2|max:20',
        ]);

        $id = $this->departments->create([
            'name' => trim($data['name']),
            'code' => strtoupper(trim($data['code'])),
            'head_of_department' => $this->request->input('head_of_department'),
            'description' => $this->request->input('description'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logs->record(auth()['id'], 'department_created', 'departments', $id, null, $data);
        flash('success', "Department '{$data['name']}' created successfully.");
        redirect('/departments');
    }

    public function update(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $did = (int)$id;

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'code' => 'required|min:2|max:20',
        ]);

        $before = $this->departments->find($did, true);
        $updateData = [
            'name' => trim($data['name']),
            'code' => strtoupper(trim($data['code'])),
            'head_of_department' => $this->request->input('head_of_department'),
            'description' => $this->request->input('description'),
        ];

        $this->departments->update($did, $updateData);
        $this->logs->record(auth()['id'], 'department_updated', 'departments', $did, $before, $updateData);

        flash('success', "Department '{$data['name']}' updated.");
        redirect('/departments');
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $did = (int)$id;

        $this->departments->softDelete($did);
        $this->logs->record(auth()['id'], 'department_deleted', 'departments', $did, null, null);

        flash('success', 'Department moved to trash.');
        redirect('/departments');
    }

    public function restore(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $did = (int)$id;

        $this->departments->restore($did);
        $this->logs->record(auth()['id'], 'department_restored', 'departments', $did, null, null);

        flash('success', 'Department restored successfully.');
        redirect('/departments?trash=1');
    }
}
