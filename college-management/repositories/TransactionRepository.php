<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class TransactionRepository extends Model
{
    protected string $table = 'transactions';

    public function recordPayment(int $feeId, int $studentId, float $amount, string $paymentMethod, ?string $notes = null, ?int $recordedBy = null): array
    {
        $txNo = 'TXN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $id = $this->create([
            'fee_id' => $feeId,
            'student_id' => $studentId,
            'transaction_no' => $txNo,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
            'recorded_by' => $recordedBy,
            'payment_date' => date('Y-m-d H:i:s'),
        ]);

        return [
            'id' => $id,
            'transaction_no' => $txNo,
            'amount' => $amount,
        ];
    }

    public function recent(int $limit = 10): array
    {
        $sql = "
            SELECT t.*, s.name as student_name, s.admission_no, f.fee_type, u.name as recorder_name
            FROM transactions t
            JOIN students s ON s.id = t.student_id
            JOIN fees f ON f.id = t.fee_id
            LEFT JOIN users u ON u.id = t.recorded_by
            ORDER BY t.payment_date DESC
            LIMIT ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findWithDetails(int $id): ?array
    {
        $sql = "
            SELECT t.*, s.name as student_name, s.admission_no, s.email as student_email, s.phone as student_phone,
                   f.amount as total_fee, f.amount_paid as total_paid, f.fee_type,
                   d.name as department_name, c.name as course_name,
                   u.name as cashier_name
            FROM transactions t
            JOIN students s ON s.id = t.student_id
            JOIN fees f ON f.id = t.fee_id
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN courses c ON c.id = s.course_id
            LEFT JOIN users u ON u.id = t.recorded_by
            WHERE t.id = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getMonthlyRevenueData(): array
    {
        $sql = "
            SELECT DATE_FORMAT(payment_date, '%b %Y') as month_label,
                   DATE_FORMAT(payment_date, '%Y-%m') as sort_key,
                   SUM(amount) as total_amount
            FROM transactions
            WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY sort_key, month_label
            ORDER BY sort_key ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }
}
