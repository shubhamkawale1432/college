<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class ActivityLogRepository extends Model
{
    protected string $table = 'activity_logs';

    public function record(
        ?int $userId,
        string $action,
        string $entityType,
        ?int $entityId,
        ?array $before,
        ?array $after
    ): void {
        $this->create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'before_data' => $before ? json_encode($before) : null,
            'after_data' => $after ? json_encode($after) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function recent(int $limit = 10): array
    {
        $sql = 'SELECT al.*, u.name AS user_name, u.role as user_role
                FROM activity_logs al
                LEFT JOIN users u ON u.id = al.user_id
                ORDER BY al.created_at DESC LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function paginateLogs(int $page = 1, int $perPage = 20, ?string $action = null, ?int $userId = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if ($action) {
            $where[] = "al.action = :action";
            $params[':action'] = $action;
        }
        if ($userId) {
            $where[] = "al.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT al.*, u.name AS user_name, u.email as user_email, u.role as user_role
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            WHERE {$whereSql}
            ORDER BY al.id DESC
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

        $countSql = "SELECT COUNT(*) FROM activity_logs al WHERE {$whereSql}";
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
}
