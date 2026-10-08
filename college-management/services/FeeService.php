<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Repositories\FeeRepository;
use Repositories\TransactionRepository;
use Repositories\ActivityLogRepository;

final class FeeService
{
    public function __construct(
        private FeeRepository $fees = new FeeRepository(),
        private TransactionRepository $transactions = new TransactionRepository(),
        private ActivityLogRepository $logs = new ActivityLogRepository()
    ) {
    }

    public function recordPayment(int $feeId, float $amount, string $paymentMethod, ?string $notes, ?int $actorId): array
    {
        Database::beginTransaction();
        try {
            $fee = $this->fees->find($feeId);
            if (!$fee) {
                throw new \RuntimeException('Fee invoice not found.');
            }
            $newPaid = (float) $fee['amount_paid'] + $amount;
            $status = $newPaid >= (float) $fee['amount'] ? 'paid' : 'partial';

            $this->fees->update($feeId, [
                'amount_paid' => $newPaid,
                'status' => $status,
                'last_payment_at' => date('Y-m-d H:i:s'),
            ]);

            $tx = $this->transactions->recordPayment(
                $feeId,
                (int)$fee['student_id'],
                $amount,
                $paymentMethod,
                $notes,
                $actorId
            );

            $this->logs->record($actorId, 'fee_payment_recorded', 'fees', $feeId, $fee, [
                'amount_paid' => $newPaid,
                'status' => $status,
                'transaction_id' => $tx['id'],
                'transaction_no' => $tx['transaction_no'],
            ]);

            Database::commit();
            return $tx;
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }
}
