<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Repositories\StudentRepository;
use Repositories\UserRepository;
use Repositories\FeeRepository;
use Repositories\CourseRepository;
use Repositories\ActivityLogRepository;

final class StudentService
{
    public function __construct(
        private StudentRepository $students = new StudentRepository(),
        private UserRepository $users = new UserRepository(),
        private FeeRepository $fees = new FeeRepository(),
        private CourseRepository $courses = new CourseRepository(),
        private ActivityLogRepository $logs = new ActivityLogRepository()
    ) {
    }

    public function enroll(array $data, ?int $actorId, ?string $photoPath = null): array
    {
        Database::beginTransaction();
        try {
            $admissionNo = $this->generateAdmissionNumber();
            $tempPassword = bin2hex(random_bytes(4));

            $userId = $this->users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
                'role' => 'student',
                'phone' => $data['phone'] ?? null,
                'avatar' => $photoPath,
                'failed_attempts' => 0,
                'two_factor_enabled' => 0,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $courseId = !empty($data['course_id']) ? (int)$data['course_id'] : null;
            $deptId = !empty($data['department_id']) ? (int)$data['department_id'] : null;

            $studentId = $this->students->create([
                'user_id' => $userId,
                'admission_no' => $admissionNo,
                'name' => $data['name'],
                'photo' => $photoPath,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? 'other',
                'department_id' => $deptId,
                'course_id' => $courseId,
                'semester' => (int)($data['semester'] ?? 1),
                'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'address' => $data['address'] ?? null,
                'guardian_name' => $data['guardian_name'] ?? null,
                'guardian_phone' => $data['guardian_phone'] ?? null,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Automatically create initial tuition fee invoice
            $feeAmount = 2500.00;
            if ($courseId) {
                $course = $this->courses->find($courseId);
                if ($course && (float)$course['fee_per_year'] > 0) {
                    $feeAmount = (float)$course['fee_per_year'];
                }
            }

            $dueDate = date('Y-m-d', strtotime('+30 days'));
            $this->fees->create([
                'student_id' => $studentId,
                'fee_type' => 'Annual Tuition Fee',
                'amount' => $feeAmount,
                'amount_paid' => 0.00,
                'due_date' => $dueDate,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->logs->record($actorId, 'student_enrolled', 'students', $studentId, null, [
                'admission_no' => $admissionNo,
                'name' => $data['name'],
                'fee_generated' => $feeAmount,
            ]);

            Database::commit();
            return ['admission_no' => $admissionNo, 'temp_password' => $tempPassword, 'student_id' => $studentId];
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data, ?int $actorId, ?string $photoPath = null): void
    {
        $before = $this->students->find($id, true);
        $updateFields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? 'other',
            'department_id' => !empty($data['department_id']) ? (int)$data['department_id'] : null,
            'course_id' => !empty($data['course_id']) ? (int)$data['course_id'] : null,
            'semester' => (int)($data['semester'] ?? 1),
            'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'address' => $data['address'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'guardian_phone' => $data['guardian_phone'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];

        if ($photoPath) {
            $updateFields['photo'] = $photoPath;
        }

        $this->students->update($id, $updateFields);

        // Also sync name/phone/avatar with linked user if present
        if (!empty($before['user_id'])) {
            $userUpd = ['name' => $data['name'], 'phone' => $data['phone'] ?? null];
            if ($photoPath) {
                $userUpd['avatar'] = $photoPath;
            }
            $this->users->update((int)$before['user_id'], $userUpd);
        }

        $after = $this->students->find($id, true);
        $this->logs->record($actorId, 'student_updated', 'students', $id, $before, $after);
    }

    public function delete(int $id, ?int $actorId): void
    {
        $before = $this->students->find($id, true);
        $this->students->softDelete($id);
        $this->logs->record($actorId, 'student_deleted', 'students', $id, $before, null);
    }

    public function restore(int $id, ?int $actorId): void
    {
        $this->students->restore($id);
        $this->logs->record($actorId, 'student_restored', 'students', $id, null, null);
    }

    private function generateAdmissionNumber(): string
    {
        $year = date('Y');
        $count = $this->students->count(true) + 1;
        return sprintf('CMS-%s-%04d', $year, $count);
    }
}
