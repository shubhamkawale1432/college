<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;

final class SettingRepository extends Model
{
    protected string $table = 'settings';

    public function allAssoc(): array
    {
        $rows = $this->db->query("SELECT setting_key, setting_value, setting_group FROM settings")->fetchAll();
        $res = [];
        foreach ($rows as $row) {
            $res[$row['setting_key']] = $row['setting_value'];
        }
        return $res;
    }

    public function set(string $key, ?string $value, string $group = 'general'): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_group)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)
        ");
        $stmt->execute([$key, $value, $group]);
    }

    public function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $val) {
            $this->set($key, is_null($val) ? null : (string)$val, $group);
        }
    }
}
