<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\DepartmentRepository;
use Repositories\CourseRepository;
use Repositories\NoticeRepository;
use Repositories\StudentRepository;
use Repositories\FacultyRepository;

final class HomeController extends Controller
{
    public function index(): void
    {
        $deptRepo = new DepartmentRepository();
        $courseRepo = new CourseRepository();
        $noticeRepo = new NoticeRepository();
        $studentRepo = new StudentRepository();
        $facultyRepo = new FacultyRepository();

        $departments = $deptRepo->all('name ASC');
        $courses = $courseRepo->all('name ASC');
        $notices = $noticeRepo->recent(4);

        $stats = [
            'students' => $studentRepo->count(),
            'faculty' => $facultyRepo->count(),
            'courses' => $courseRepo->count(),
            'departments' => $deptRepo->count(),
        ];

        $this->view('home.index', [
            'title' => setting('institute_name', config('name')) . ' - Academic Excellence Portal',
            'departments' => $departments,
            'courses' => $courses,
            'notices' => $notices,
            'stats' => $stats,
            'isAuth' => is_authenticated(),
            'user' => auth(),
        ]);
    }
}
