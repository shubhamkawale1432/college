<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\UserRepository;
use Repositories\StudentRepository;
use Repositories\FacultyRepository;
use Repositories\ActivityLogRepository;
use Services\UploadService;

final class ProfileController extends Controller
{
    private UserRepository $users;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $userId = auth()['id'];
        $user = $this->users->find($userId);
        $roleData = null;

        if ($user['role'] === 'student') {
            $students = new StudentRepository();
            $roleData = $students->findByUserId($userId);
        } elseif ($user['role'] === 'faculty') {
            $faculty = new FacultyRepository();
            $roleData = $faculty->findByUserId($userId);
        }

        $this->view('profile.index', [
            'user' => $user,
            'roleData' => $roleData,
            'title' => 'My Profile',
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();
        $userId = auth()['id'];
        $user = $this->users->find($userId);

        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'phone' => 'max:20',
        ]);

        $updateData = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'two_factor_enabled' => (int)($this->request->input('two_factor_enabled') ? 1 : 0),
        ];

        // Handle Avatar Upload
        if (!empty($_FILES['avatar']['name'])) {
            try {
                $avatarPath = UploadService::saveImage($_FILES['avatar'], 'avatars');
                if ($avatarPath) {
                    $updateData['avatar'] = $avatarPath;
                    $_SESSION['user']['avatar'] = $avatarPath;
                }
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/profile');
            }
        }

        $this->users->update($userId, $updateData);

        // Update session user name
        $_SESSION['user']['name'] = $data['name'];

        $this->logs->record($userId, 'profile_updated', 'users', $userId, $user, $updateData);

        flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }
}
