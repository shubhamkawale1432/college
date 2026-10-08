<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class StudentRepository extends Model
{
    protected string $table = 'students';
    protected bool $supportsSoftDelete = true;

    public function paginateStudents(
        int $page = 1,
        int $perPage = 20,
        ?string $search = null,
        ?int $deptId = null,
        ?int $courseId = null,
        ?string $status = null,
        bool $trash = false
    ): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = $trash ? ["s.deleted_at IS NOT NULL"] : ["s.deleted_at IS NULL"];
        $params = [];

        if ($search) {
            $where[] = "(s.name LIKE :q OR s.admission_no LIKE :q OR s.email LIKE :q OR s.phone LIKE :q)";
            $params[':q'] = "%{$search}%";
        }
        if ($deptId) {
            $where[] = "s.department_id = :dept_id";
            $params[':dept_id'] = $deptId;
        }
        if ($courseId) {
            $where[] = "s.course_id = :course_id";
            $params[':course_id'] = $courseId;
        }
        if ($status) {
            $where[] = "s.status = :status";
            $params[':status'] = $status;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT s.*, c.name AS course_name, c.code AS course_code, d.name AS department_name, d.code AS department_code
            FROM students s
            LEFT JOIN courses c ON c.id = s.course_id
            LEFT JOIN departments d ON d.id = s.department_id
            WHERE {$whereSql}
            ORDER BY s.id DESC
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

        $countSql = "SELECT COUNT(*) FROM students s WHERE {$whereSql}";
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

    public function findWithRelations(int $id, bool $includeTrashed = true): ?array
    {
        $sql = 'SELECT s.*, c.name AS course_name, c.code AS course_code, d.name AS department_name, d.code AS department_code
                FROM students s
                LEFT JOIN courses c ON c.id = s.course_id
                LEFT JOIN departments d ON d.id = s.department_id
                WHERE s.id = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE user_id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function countByDepartment(): array
    {
        $sql = 'SELECT d.name, COUNT(s.id) AS total
                FROM departments d
                LEFT JOIN students s ON s.department_id = d.id AND s.deleted_at IS NULL
                WHERE d.deleted_at IS NULL
                GROUP BY d.id, d.name
                ORDER BY total DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function exportAll(?int $deptId = null, ?int $courseId = null): array
    {
        $where = ["s.deleted_at IS NULL"];
        $params = [];
        if ($deptId) {
            $where[] = "s.department_id = :dept_id";
            $params[':dept_id'] = $deptId;
        }
        if ($courseId) {
            $where[] = "s.course_id = :course_id";
            $params[':course_id'] = $courseId;
        }
        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT s.admission_no, s.name, s.email, s.phone, s.gender, s.semester, s.status,
                   d.name as department, c.name as course, s.created_at
            FROM students s
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN courses c ON c.id = s.course_id
            WHERE {$whereSql}
            ORDER BY s.admission_no ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
