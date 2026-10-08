<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class FacultyRepository extends Model
{
    protected string $table = 'faculty';
    protected bool $supportsSoftDelete = true;

    public function paginateFaculty(int $page = 1, int $perPage = 20, ?string $search = null, ?int $deptId = null, bool $trash = false): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = $trash ? ["f.deleted_at IS NOT NULL"] : ["f.deleted_at IS NULL"];
        $params = [];

        if ($search) {
            $where[] = "(f.name LIKE :q OR f.email LIKE :q OR f.designation LIKE :q)";
            $params[':q'] = "%{$search}%";
        }
        if ($deptId) {
            $where[] = "f.department_id = :dept_id";
            $params[':dept_id'] = $deptId;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT f.*, d.name AS department_name, d.code AS department_code
            FROM faculty f
            LEFT JOIN departments d ON d.id = f.department_id
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

        $countSql = "SELECT COUNT(*) FROM faculty f WHERE {$whereSql}";
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

    public function withDepartment(): array
    {
        $sql = 'SELECT f.*, d.name AS department_name
                FROM faculty f
                LEFT JOIN departments d ON d.id = f.department_id
                WHERE f.deleted_at IS NULL
                ORDER BY f.name';
        return $this->db->query($sql)->fetchAll();
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM faculty WHERE user_id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }
}
