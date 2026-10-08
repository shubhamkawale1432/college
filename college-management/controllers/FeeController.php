<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\FeeRepository;
use Repositories\StudentRepository;
use Repositories\TransactionRepository;
use Services\FeeService;

final class FeeController extends Controller
{
    private FeeRepository $fees;
    private StudentRepository $students;
    private TransactionRepository $transactions;
    private FeeService $service;

    public function __construct()
    {
        parent::__construct();
        $this->fees = new FeeRepository();
        $this->students = new StudentRepository();
        $this->transactions = new TransactionRepository();
        $this->service = new FeeService();
    }

    public function index(): void
    {
        $role = auth_role();
        $page = (int)$this->request->input('page', 1);
        $status = $this->request->input('status');
        $search = $this->request->input('q');

        $studentFilter = null;
        if ($role === 'student') {
            $student = $this->students->findByUserId(auth()['id']);
            $studentFilter = $student['id'] ?? -1;
        }

        $pagination = $this->fees->paginateWithStudents($page, 15, $status, $studentFilter, $search);
        $recentTransactions = $this->transactions->recent(6);
        $allStudents = has_role('admin', 'operator') ? $this->students->all('name') : [];

        $this->view('fees.index', [
            'pagination' => $pagination,
            'recentTransactions' => $recentTransactions,
            'students' => $allStudents,
            'outstanding' => $this->fees->outstandingTotal(),
            'totalCollected' => $this->fees->totalCollected(),
            'currentStatus' => $status,
            'search' => $search,
            'title' => 'Fee Management & Invoices',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'student_id' => 'required|numeric',
            'fee_type' => 'required|max:100',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
        ]);

        $this->fees->create([
            'student_id' => (int)$data['student_id'],
            'fee_type' => trim($data['fee_type']),
            'amount' => (float)$data['amount'],
            'amount_paid' => 0.00,
            'due_date' => $data['due_date'],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        flash('success', 'Fee invoice issued successfully.');
        redirect('/fees');
    }

    public function pay(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $fid = (int)$id;

        $data = $this->validate([
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,card,upi_online,bank_transfer,cheque',
        ]);

        $amount = (float)$data['amount'];
        if ($amount <= 0) {
            flash('error', 'Payment amount must be greater than zero.');
            redirect('/fees');
        }

        try {
            $tx = $this->service->recordPayment(
                $fid,
                $amount,
                $data['payment_method'],
                $this->request->input('notes'),
                auth()['id']
            );

            flash('success', "Payment of " . format_currency($amount) . " recorded. Transaction #{$tx['transaction_no']}");
            redirect("/fees/receipt/{$tx['id']}");
        } catch (\Throwable $e) {
            flash('error', 'Payment failed: ' . $e->getMessage());
            redirect('/fees');
        }
    }

    public function receipt(string $id): void
    {
        $txId = (int)$id;
        $transaction = $this->transactions->findWithDetails($txId);
        if (!$transaction) {
            flash('error', 'Transaction record not found.');
            redirect('/fees');
        }

        // Student can only view their own receipt
        if (has_role('student')) {
            $student = $this->students->findByUserId(auth()['id']);
            if (!$student || (int)$transaction['student_id'] !== (int)$student['id']) {
                http_response_code(403);
                $this->view('errors.403');
                return;
            }
        }

        $this->view('fees.receipt', [
            'tx' => $transaction,
            'title' => "Payment Receipt #{$transaction['transaction_no']}",
        ]);
    }
}
