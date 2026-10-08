<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class AttendanceRepository extends Model
{
    protected string $table = 'attendance';

    public function forStudentAndDate(int $studentId, string $date): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1');
        $stmt->execute([$studentId, $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function byCourseAndDate(int $courseId, string $date): array
    {
        $sql = 'SELECT a.*, s.name AS student_name, s.admission_no
                FROM attendance a
                JOIN students s ON s.id = a.student_id
                WHERE s.course_id = ? AND a.attendance_date = ?
                ORDER BY s.name';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId, $date]);
        return $stmt->fetchAll();
    }

    public function studentSummary(int $studentId): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_days,
                    SUM(status = 'present') AS present_days,
                    SUM(status = 'absent') AS absent_days
                FROM attendance WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch() ?: ['total_days' => 0, 'present_days' => 0, 'absent_days' => 0];
    }

    public function forStudent(int $studentId, int $limit = 100): array
    {
        $sql = 'SELECT a.*, a.attendance_date AS date, u.name AS recorder_name, c.name AS course_name
                FROM attendance a
                LEFT JOIN users u ON u.id = a.marked_by
                LEFT JOIN students s ON s.id = a.student_id
                LEFT JOIN courses c ON c.id = s.course_id
                WHERE a.student_id = ?
                ORDER BY a.attendance_date DESC
                LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $studentId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
