<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class ComplaintRepository extends Model
{
    protected string $table = 'complaints';
    protected bool $supportsSoftDelete = true;

    public function allWithUsers(int $page = 1, int $perPage = 20, ?string $status = null, ?int $userId = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = ["c.deleted_at IS NULL"];
        $params = [];

        if ($status) {
            $where[] = "c.status = :status";
            $params[':status'] = $status;
        }

        if ($userId !== null) {
            $where[] = "c.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT c.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM complaints c
            JOIN users u ON u.id = c.user_id
            WHERE {$whereSql}
            ORDER BY c.created_at DESC
            LIMIT :__limit OFFSET :__offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':__limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':__offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM complaints c WHERE {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public function findWithUser(int $id): ?array
    {
        $sql = "
            SELECT c.*, u.name as user_name, u.email as user_email, u.role as user_role
            FROM complaints c
            JOIN users u ON u.id = c.user_id
            WHERE c.id = ? AND c.deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createTicket(array $data): int
    {
        $data['ticket_no'] = 'TKT-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        return $this->create($data);
    }
}
