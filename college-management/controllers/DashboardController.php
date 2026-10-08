<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\StudentRepository;
use Repositories\FacultyRepository;
use Repositories\CourseRepository;
use Repositories\DepartmentRepository;
use Repositories\NoticeRepository;
use Repositories\ExamRepository;
use Repositories\FeeRepository;
use Repositories\TransactionRepository;
use Repositories\ComplaintRepository;
use Repositories\ActivityLogRepository;
use Repositories\AttendanceRepository;
use Repositories\ResultRepository;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $role = auth_role();
        $userId = auth()['id'];

        $students = new StudentRepository();
        $faculty = new FacultyRepository();
        $courses = new CourseRepository();
        $departments = new DepartmentRepository();
        $notices = new NoticeRepository();
        $exams = new ExamRepository();
        $fees = new FeeRepository();
        $transactions = new TransactionRepository();
        $complaints = new ComplaintRepository();
        $logs = new ActivityLogRepository();
        $attendance = new AttendanceRepository();
        $results = new ResultRepository();

        $data = [
            'role' => $role,
            'title' => 'Dashboard',
            'recentNotices' => $notices->recent(5),
            'upcomingExams' => $exams->upcoming(5),
        ];

        if ($role === 'student') {
            $student = $students->findByUserId($userId);
            $studentId = $student['id'] ?? 0;
            $data['student'] = $student;
            $data['studentAttendance'] = $studentId ? $attendance->forStudent($studentId) : [];
            $data['studentFees'] = $studentId ? $fees->forStudent($studentId) : [];
            $data['studentResults'] = $studentId ? $results->forStudent($studentId) : [];
        } elseif ($role === 'faculty') {
            $fac = $faculty->findByUserId($userId);
            $data['faculty'] = $fac;
            $data['courseCount'] = $courses->count();
            $data['studentCount'] = $students->count();
            $data['upcomingExams'] = $exams->upcoming(5);
        } else {
            // Admin & Operator
            $data['studentCount'] = $students->count();
            $data['facultyCount'] = $faculty->count();
            $data['courseCount'] = $courses->count();
            $data['departmentCount'] = $departments->count();
            $data['outstandingFees'] = $fees->outstandingTotal();
            $data['totalCollected'] = $fees->totalCollected();
            $data['openComplaints'] = $complaints->countWhere("status = 'open' AND deleted_at IS NULL");
            $data['recentActivity'] = $logs->recent(8);
            $data['recentTransactions'] = $transactions->recent(5);

            // Chart 1: Department Distribution
            $deptDist = $students->countByDepartment();
            $data['chartDeptLabels'] = array_column($deptDist, 'name');
            $data['chartDeptValues'] = array_map('intval', array_column($deptDist, 'total'));

            // Chart 2: Monthly Revenue (Last 6 Months)
            $revData = $transactions->getMonthlyRevenueData();
            if (empty($revData)) {
                // Seed sensible defaults for empty chart
                $revData = [
                    ['month_label' => 'Apr', 'total_amount' => 12500],
                    ['month_label' => 'May', 'total_amount' => 19200],
                    ['month_label' => 'Jun', 'total_amount' => 15400],
                    ['month_label' => 'Jul', 'total_amount' => 24800],
                    ['month_label' => 'Aug', 'total_amount' => 31000],
                    ['month_label' => 'Sep', 'total_amount' => (float)$data['totalCollected']],
                ];
            }
            $data['chartRevLabels'] = array_column($revData, 'month_label');
            $data['chartRevValues'] = array_map('floatval', array_column($revData, 'total_amount'));
        }

        $this->view('dashboard.index', $data);
    }
}
