<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class NotificationRepository extends Model
{
    protected string $table = 'notifications';

    public function forUser(int $userId, string $role, int $limit = 20): array
    {
        $sql = "
            SELECT * FROM notifications
            WHERE user_id = ? OR (user_id IS NULL AND (target_role IS NULL OR target_role = ?))
            ORDER BY created_at DESC
            LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $role, PDO::PARAM_STR);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsRead(int $id, int $userId): void
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function markAllAsRead(int $userId, string $role): void
    {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1
            WHERE user_id = ? OR (user_id IS NULL AND (target_role IS NULL OR target_role = ?))
        ");
        $stmt->execute([$userId, $role]);
    }

    public function broadcast(string $title, string $message, string $type = 'info', ?string $targetRole = null, ?string $link = null): int
    {
        return $this->create([
            'user_id' => null,
            'target_role' => $targetRole,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => 0,
        ]);
    }
}
