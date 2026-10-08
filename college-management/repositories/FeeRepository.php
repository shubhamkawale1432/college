<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class FeeRepository extends Model
{
    protected string $table = 'fees';
    protected bool $supportsSoftDelete = true;

    public function paginateWithStudents(
        int $page = 1,
        int $perPage = 20,
        ?string $status = null,
        ?int $studentId = null,
        ?string $search = null
    ): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = ["f.deleted_at IS NULL"];
        $params = [];

        if ($status) {
            $where[] = "f.status = :status";
            $params[':status'] = $status;
        }
        if ($studentId) {
            $where[] = "f.student_id = :student_id";
            $params[':student_id'] = $studentId;
        }
        if ($search) {
            $where[] = "(s.name LIKE :q OR s.admission_no LIKE :q OR f.fee_type LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT f.*, s.name as student_name, s.admission_no, s.email as student_email,
                   d.name as department_name, c.name as course_name
            FROM fees f
            JOIN students s ON s.id = f.student_id
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN courses c ON c.id = s.course_id
            WHERE {$whereSql}
            ORDER BY f.id DESC
            LIMIT :__limit OFFSET :__offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':__limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':__offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM fees f JOIN students s ON s.id = f.student_id WHERE {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public function findWithStudent(int $id): ?array
    {
        $sql = "
            SELECT f.*, s.name as student_name, s.admission_no, s.email as student_email, s.phone as student_phone,
                   d.name as department_name, c.name as course_name
            FROM fees f
            JOIN students s ON s.id = f.student_id
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN courses c ON c.id = s.course_id
            WHERE f.id = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function forStudent(int $studentId): array
    {
        $sql = "SELECT * FROM fees WHERE student_id = ? AND deleted_at IS NULL ORDER BY due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function outstandingTotal(): float
    {
        $sql = "SELECT COALESCE(SUM(amount - amount_paid), 0) FROM fees WHERE status != 'paid' AND deleted_at IS NULL";
        return (float) $this->db->query($sql)->fetchColumn();
    }

    public function totalCollected(): float
    {
        $sql = "SELECT COALESCE(SUM(amount_paid), 0) FROM fees WHERE deleted_at IS NULL";
        return (float) $this->db->query($sql)->fetchColumn();
    }
}
