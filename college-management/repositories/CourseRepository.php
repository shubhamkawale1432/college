<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class CourseRepository extends Model
{
    protected string $table = 'courses';
    protected bool $supportsSoftDelete = true;

    public function withDepartment(bool $trash = false): array
    {
        $delClause = $trash ? "c.deleted_at IS NOT NULL" : "c.deleted_at IS NULL";
        $sql = "
            SELECT c.*, d.name AS department_name, d.code AS department_code,
                   (SELECT COUNT(*) FROM students s WHERE s.course_id = c.id AND s.deleted_at IS NULL) as student_count
            FROM courses c
            LEFT JOIN departments d ON d.id = c.department_id
            WHERE {$delClause}
            ORDER BY c.name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }
}
