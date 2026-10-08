<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class NoticeRepository extends Model
{
    protected string $table = 'notices';

    public function recent(int $limit = 5): array
    {
        $stmt = $this->db->prepare('SELECT * FROM notices ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
