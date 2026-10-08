<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class RoleRepository extends Model
{
    protected string $table = 'roles';

    public function allWithUserCount(): array
    {
        $sql = "
            SELECT r.*, COUNT(u.id) as user_count,
            (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) as permission_count
            FROM roles r
            LEFT JOIN users u ON u.role = r.name AND u.deleted_at IS NULL
            GROUP BY r.id
            ORDER BY r.id ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        return $stmt->fetch() ?: null;
    }

    public function getPermissions(int $roleId): array
    {
        $sql = "
            SELECT p.*, (rp.role_id IS NOT NULL) as assigned
            FROM permissions p
            LEFT JOIN role_permissions rp ON rp.permission_id = p.id AND rp.role_id = ?
            ORDER BY p.module ASC, p.name ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $del = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $del->execute([$roleId]);

        if (!empty($permissionIds)) {
            $ins = $this->db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permissionIds as $pid) {
                $ins->execute([$roleId, (int)$pid]);
            }
        }
    }
}
