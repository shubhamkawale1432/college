<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Repositories\AttendanceRepository;
use Repositories\ActivityLogRepository;

final class AttendanceService
{
    public function __construct(
        private AttendanceRepository $attendance = new AttendanceRepository(),
        private ActivityLogRepository $logs = new ActivityLogRepository()
    ) {
    }

    /**
     * Bulk-marks attendance for a whole course/date in one transaction.
     * $records = [ ['student_id' => 1, 'status' => 'present'], ... ]
     */
    public function markBulk(int $courseId, string $date, array $records, ?int $actorId): int
    {
        Database::beginTransaction();
        try {
            $count = 0;
            foreach ($records as $record) {
                $existing = $this->attendance->forStudentAndDate((int) $record['student_id'], $date);
                if ($existing) {
                    $this->attendance->update($existing['id'], ['status' => $record['status']]);
                } else {
                    $this->attendance->create([
                        'student_id' => $record['student_id'],
                        'attendance_date' => $date,
                        'status' => $record['status'],
                        'marked_by' => $actorId,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $count++;
            }
            $this->logs->record($actorId, 'attendance_marked', 'attendance', null, null, [
                'course_id' => $courseId, 'date' => $date, 'count' => $count,
            ]);
            Database::commit();
            return $count;
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }
}
