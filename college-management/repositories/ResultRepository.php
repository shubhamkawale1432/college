<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class ResultRepository extends Model
{
    protected string $table = 'results';

    public function forStudent(int $studentId): array
    {
        $sql = 'SELECT r.*, e.title AS exam_title, e.exam_date
                FROM results r
                JOIN exams e ON e.id = r.exam_id
                WHERE r.student_id = ?
                ORDER BY e.exam_date DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function forExam(int $examId): array
    {
        $sql = 'SELECT r.*, s.name AS student_name, s.admission_no
                FROM results r
                JOIN students s ON s.id = r.student_id
                WHERE r.exam_id = ?
                ORDER BY s.name';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$examId]);
        return $stmt->fetchAll();
    }
}
