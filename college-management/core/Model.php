<?php
declare(strict_types=1);

namespace Core;

use PDO;

/**
 * Base repository providing generic CRUD over a single table using
 * PDO prepared statements exclusively with soft-delete and query filtering.
 */
abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $supportsSoftDelete = false;
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function find(int|string $id, bool $includeTrashed = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        if ($this->supportsSoftDelete && !$includeTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = null, bool $includeTrashed = false): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($this->supportsSoftDelete && !$includeTrashed) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function paginate(int $page, int $perPage, string $orderBy = null, bool $includeTrashed = false): array
    {
        $where = ($this->supportsSoftDelete && !$includeTrashed) ? 'deleted_at IS NULL' : '';
        return $this->paginateFilter($page, $perPage, $where, [], $orderBy);
    }

    public function paginateFilter(
        int $page,
        int $perPage,
        string $whereClause = '',
        array $bindings = [],
        string $orderBy = null
    ): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";
        if ($whereClause !== '') {
            $sql .= " WHERE {$whereClause}";
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $sql .= " LIMIT :__limit OFFSET :__offset";

        $stmt = $this->db->prepare($sql);
        foreach ($bindings as $key => $val) {
            $paramName = is_int($key) ? $key + 1 : (str_starts_with((string)$key, ':') ? (string)$key : ":{$key}");
            $stmt->bindValue($paramName, $val);
        }
        $stmt->bindValue(':__limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':__offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        if ($whereClause !== '') {
            $countSql .= " WHERE {$whereClause}";
        }
        $countStmt = $this->db->prepare($countSql);
        foreach ($bindings as $key => $val) {
            $paramName = is_int($key) ? $key + 1 : (str_starts_with((string)$key, ':') ? (string)$key : ":{$key}");
            $countStmt->bindValue($paramName, $val);
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

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ":{$c}", $columns);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        $assignments = implode(', ', array_map(fn($c) => "{$c} = :{$c}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$assignments} WHERE {$this->primaryKey} = :__id";
        $data['__id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int|string $id): bool
    {
        if ($this->supportsSoftDelete) {
            return $this->softDelete($id);
        }
        return $this->forceDelete($id);
    }

    public function softDelete(int|string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int|string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int|string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function where(string $column, $value, bool $includeTrashed = false): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        if ($this->supportsSoftDelete && !$includeTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function count(bool $includeTrashed = false): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($this->supportsSoftDelete && !$includeTrashed) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function countWhere(string $whereClause, array $bindings = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }
}
