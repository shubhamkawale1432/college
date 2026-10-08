<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\UserRepository;
use Repositories\RoleRepository;
use Repositories\ActivityLogRepository;

final class UserController extends Controller
{
    private UserRepository $users;
    private RoleRepository $roles;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->roles = new RoleRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin');
        $page = (int)$this->request->input('page', 1);
        $search = $this->request->input('q');
        $role = $this->request->input('role');
        $trash = (bool)$this->request->input('trash', false);

        $pagination = $this->users->paginateUsers($page, 15, $search, $role, $trash);
        $allRoles = $this->roles->all();

        $this->view('users.index', [
            'pagination' => $pagination,
            'roles' => $allRoles,
            'search' => $search,
            'currentRole' => $role,
            'trash' => $trash,
            'title' => 'User Management',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,operator,faculty,student',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($this->users->findByEmail($data['email'], true)) {
            flash('error', 'A user with this email already exists.');
            redirect('/users');
        }

        $userId = $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'phone' => $this->request->input('phone'),
            'status' => $data['status'],
        ]);

        $this->logs->record(auth()['id'], 'user_created', 'users', $userId, null, [
            'email' => $data['email'], 'role' => $data['role'],
        ]);

        flash('success', "User {$data['name']} created successfully.");
        redirect('/users');
    }

    public function update(string $id): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $uid = (int)$id;
        $user = $this->users->find($uid, true);
        if (!$user) {
            flash('error', 'User not found.');
            redirect('/users');
        }

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'role' => 'required|in:admin,operator,faculty,student',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => $this->request->input('phone'),
            'status' => $data['status'],
        ];

        $newPassword = $this->request->input('password');
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->users->update($uid, $updateData);
        $this->logs->record(auth()['id'], 'user_updated', 'users', $uid, $user, $updateData);

        flash('success', "User {$data['name']} updated.");
        redirect('/users');
    }

    public function unlock(string $id): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $this->users->unlock((int)$id);
        $this->logs->record(auth()['id'], 'user_unlocked', 'users', (int)$id, null, null);
        flash('success', 'User account unlocked.');
        redirect('/users');
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $uid = (int)$id;

        if ($uid === auth()['id']) {
            flash('error', 'You cannot delete your own account.');
            redirect('/users');
        }

        $this->users->softDelete($uid);
        $this->logs->record(auth()['id'], 'user_deleted', 'users', $uid, null, null);
        flash('success', 'User moved to trash.');
        redirect('/users');
    }

    public function restore(string $id): void
    {
        $this->authorize('admin');
        $this->requireCsrf();
        $this->users->restore((int)$id);
        $this->logs->record(auth()['id'], 'user_restored', 'users', (int)$id, null, null);
        flash('success', 'User restored successfully.');
        redirect('/users?trash=1');
    }
}
