<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\RoleRepository;
use Repositories\ActivityLogRepository;

final class RoleController extends Controller
{
    private RoleRepository $roles;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->roles = new RoleRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin');
        $allRoles = $this->roles->allWithUserCount();
        $selectedRoleId = (int)$this->request->input('role_id', $allRoles[0]['id'] ?? 1);
        $permissions = $this->roles->getPermissions($selectedRoleId);

        // Group permissions by module
        $groupedPermissions = [];
        foreach ($permissions as $p) {
            $groupedPermissions[$p['module']][] = $p;
        }

        $this->view('roles.index', [
            'roles' => $allRoles,
            'selectedRoleId' => $selectedRoleId,
            'groupedPermissions' => $groupedPermissions,
            'title' => 'Role & Permission Management',
        ]);
    }

    public function updatePermissions(string $id): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $roleId = (int)$id;
        $role = $this->roles->find($roleId);
        if (!$role) {
            flash('error', 'Role not found.');
            redirect('/roles');
        }

        $permissionIds = $this->request->input('permissions') ?? [];
        if (!is_array($permissionIds)) {
            $permissionIds = [];
        }

        $this->roles->syncPermissions($roleId, $permissionIds);
        $this->logs->record(auth()['id'], 'role_permissions_updated', 'roles', $roleId, null, [
            'role' => $role['name'], 'permission_count' => count($permissionIds),
        ]);

        flash('success', "Permissions updated for {$role['label']}.");
        redirect("/roles?role_id={$roleId}");
    }

    public function store(): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $data = $this->validate([
            'name' => 'required|min:2|max:50',
            'label' => 'required|min:2|max:100',
            'description' => 'max:255',
        ]);

        $existing = $this->roles->findByName($data['name']);
        if ($existing) {
            flash('error', 'A role with this name already exists.');
            redirect('/roles');
        }

        $roleId = $this->roles->create([
            'name' => strtolower(trim($data['name'])),
            'label' => trim($data['label']),
            'description' => $data['description'] ?? null,
        ]);

        $this->logs->record(auth()['id'], 'role_created', 'roles', $roleId, null, $data);
        flash('success', "New role '{$data['label']}' created.");
        redirect("/roles?role_id={$roleId}");
    }
}
