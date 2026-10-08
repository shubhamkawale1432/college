<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class UserRepository extends Model
{
    protected string $table = 'users';
    protected bool $supportsSoftDelete = true;

    public function findByEmail(string $email, bool $includeTrashed = false): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        if (!$includeTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE reset_token = ? AND reset_expires_at > NOW() AND deleted_at IS NULL 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function paginateUsers(int $page = 1, int $perPage = 20, ?string $search = null, ?string $role = null, bool $trash = false): array
    {
        $where = $trash ? ["deleted_at IS NOT NULL"] : ["deleted_at IS NULL"];
        $params = [];

        if ($search) {
            $where[] = "(name LIKE :q OR email LIKE :q OR phone LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        if ($role) {
            $where[] = "role = :role";
            $params[':role'] = $role;
        }

        $whereClause = implode(' AND ', $where);
        return $this->paginateFilter($page, $perPage, $whereClause, $params, "id DESC");
    }

    public function unlock(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
