<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class ApiTokenRepository extends Model
{
    protected string $table = 'api_tokens';

    public function issue(int $userId, string $name, int $expiresInDays = 30): string
    {
        $plain = bin2hex(random_bytes(32));
        $this->create([
            'user_id' => $userId,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => date('Y-m-d H:i:s', time() + $expiresInDays * 86400),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $plain;
    }

    public function findValidByPlainToken(string $plain): ?array
    {
        $hash = hash('sha256', $plain);
        $stmt = $this->db->prepare(
            'SELECT * FROM api_tokens WHERE token_hash = ? AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute([$hash]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
