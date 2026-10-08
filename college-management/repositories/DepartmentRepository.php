<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class DepartmentRepository extends Model
{
    protected string $table = 'departments';
    protected bool $supportsSoftDelete = true;

    public function allWithStats(bool $trash = false): array
    {
        $delClause = $trash ? "d.deleted_at IS NOT NULL" : "d.deleted_at IS NULL";
        $sql = "
            SELECT d.*, 
                   COUNT(DISTINCT c.id) as course_count,
                   COUNT(DISTINCT s.id) as student_count,
                   COUNT(DISTINCT f.id) as faculty_count
            FROM departments d
            LEFT JOIN courses c ON c.department_id = d.id AND c.deleted_at IS NULL
            LEFT JOIN students s ON s.department_id = d.id AND s.deleted_at IS NULL
            LEFT JOIN faculty f ON f.department_id = d.id AND f.deleted_at IS NULL
            WHERE {$delClause}
            GROUP BY d.id
            ORDER BY d.name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }
}
