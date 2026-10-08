<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\LibraryRepository;
use Repositories\StudentRepository;
use Repositories\ActivityLogRepository;

final class LibraryController extends Controller
{
    private LibraryRepository $library;
    private StudentRepository $students;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->library = new LibraryRepository();
        $this->students = new StudentRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $page = (int)$this->request->input('page', 1);
        $search = $this->request->input('q');
        $tab = $this->request->input('tab', 'books');
        $status = $this->request->input('status');

        $booksPagination = $this->library->books($page, 15, $search);
        $issuesPagination = $this->library->issues($page, 15, $status);
        $studentsList = $this->students->all('name');

        $this->view('library.index', [
            'books' => $booksPagination,
            'issues' => $issuesPagination,
            'students' => $studentsList,
            'search' => $search,
            'currentTab' => $tab,
            'currentStatus' => $status,
            'title' => 'Library & Resource Inventory',
        ]);
    }

    public function storeBook(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'isbn' => 'required|min:5|max:30',
            'title' => 'required|min:2|max:200',
            'author' => 'required|min:2|max:150',
            'category' => 'required|max:100',
            'total_copies' => 'required|numeric',
        ]);

        $copies = (int)$data['total_copies'];
        $bookId = $this->library->create([
            'isbn' => trim($data['isbn']),
            'title' => trim($data['title']),
            'author' => trim($data['author']),
            'category' => trim($data['category']),
            'total_copies' => $copies,
            'available_copies' => $copies,
            'rack_location' => $this->request->input('rack_location'),
        ]);

        $this->logs->record(auth()['id'], 'book_added', 'library_books', $bookId, null, $data);
        flash('success', "Book '{$data['title']}' added to library catalog.");
        redirect('/library?tab=books');
    }

    public function issueBook(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $data = $this->validate([
            'book_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'due_date' => 'required|date',
        ]);

        $success = $this->library->issueBook(
            (int)$data['book_id'],
            (int)$data['student_id'],
            $data['due_date']
        );

        if (!$success) {
            flash('error', 'Book is currently out of stock or unavailable for issue.');
            redirect('/library?tab=books');
        }

        $this->logs->record(auth()['id'], 'book_issued', 'library_issues', (int)$data['book_id'], null, $data);
        flash('success', 'Book issued successfully to student.');
        redirect('/library?tab=issues');
    }

    public function returnBook(string $id): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $issueId = (int)$id;
        $success = $this->library->returnBook($issueId);

        if (!$success) {
            flash('error', 'Unable to process return.');
            redirect('/library?tab=issues');
        }

        $this->logs->record(auth()['id'], 'book_returned', 'library_issues', $issueId, null, null);
        flash('success', 'Book returned to inventory successfully.');
        redirect('/library?tab=issues');
    }
}
