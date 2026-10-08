<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\StudentRepository;
use Repositories\FeeRepository;
use Repositories\TransactionRepository;
use Repositories\DepartmentRepository;
use Repositories\CourseRepository;
use Repositories\AttendanceRepository;
use Repositories\ResultRepository;

final class ReportController extends Controller
{
    private StudentRepository $students;
    private FeeRepository $fees;
    private TransactionRepository $transactions;
    private DepartmentRepository $departments;
    private CourseRepository $courses;

    public function __construct()
    {
        parent::__construct();
        $this->students = new StudentRepository();
        $this->fees = new FeeRepository();
        $this->transactions = new TransactionRepository();
        $this->departments = new DepartmentRepository();
        $this->courses = new CourseRepository();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator', 'faculty');

        $depts = $this->departments->all('name');
        $courses = $this->courses->all('name');

        $this->view('reports.index', [
            'departments' => $depts,
            'courses' => $courses,
            'title' => 'Reports & Analytics Center',
        ]);
    }

    public function exportCsv(): void
    {
        $this->authorize('admin', 'operator');
        $type = $this->request->input('type', 'students');

        if ($type === 'students') {
            $deptId = $this->request->input('dept_id') ? (int)$this->request->input('dept_id') : null;
            $courseId = $this->request->input('course_id') ? (int)$this->request->input('course_id') : null;
            $data = $this->students->exportAll($deptId, $courseId);

            $this->outputCsv('students_report_' . date('Ymd_His') . '.csv', [
                'Admission No', 'Name', 'Email', 'Phone', 'Gender', 'Semester', 'Status', 'Department', 'Course', 'Admission Date',
            ], $data);
        } elseif ($type === 'transactions') {
            $sql = "
                SELECT t.transaction_no, s.admission_no, s.name as student_name, f.fee_type,
                       t.amount, t.payment_method, t.payment_date, t.notes
                FROM transactions t
                JOIN students s ON s.id = t.student_id
                JOIN fees f ON f.id = t.fee_id
                ORDER BY t.payment_date DESC
            ";
            $db = \Core\Database::connection();
            $data = $db->query($sql)->fetchAll();

            $this->outputCsv('transactions_report_' . date('Ymd_His') . '.csv', [
                'Transaction No', 'Admission No', 'Student Name', 'Fee Type', 'Amount', 'Payment Method', 'Payment Date', 'Notes',
            ], $data);
        } elseif ($type === 'fees_outstanding') {
            $sql = "
                SELECT s.admission_no, s.name, s.email, f.fee_type, f.amount, f.amount_paid,
                       (f.amount - f.amount_paid) as balance, f.due_date, f.status
                FROM fees f
                JOIN students s ON s.id = f.student_id
                WHERE f.status != 'paid' AND f.deleted_at IS NULL
                ORDER BY balance DESC
            ";
            $db = \Core\Database::connection();
            $data = $db->query($sql)->fetchAll();

            $this->outputCsv('outstanding_fees_' . date('Ymd_His') . '.csv', [
                'Admission No', 'Student Name', 'Email', 'Fee Type', 'Total Fee', 'Amount Paid', 'Balance Due', 'Due Date', 'Status',
            ], $data);
        } else {
            flash('error', 'Unknown report export type.');
            redirect('/reports');
        }
    }

    private function outputCsv(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel compatibility
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            fputcsv($out, array_values($row));
        }

        fclose($out);
        exit;
    }
}
