<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class ExamRepository extends Model
{
    protected string $table = 'exams';

    public function upcoming(int $limit = 5): array
    {
        $stmt = $this->db->prepare('SELECT * FROM exams WHERE exam_date >= CURDATE() ORDER BY exam_date ASC LIMIT ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
