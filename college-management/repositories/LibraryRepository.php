<?php
declare(strict_types=1);

namespace Repositories;

use Core\Model;
use PDO;

final class LibraryRepository extends Model
{
    protected string $table = 'library_books';

    public function books(int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        $where = "1=1";
        $params = [];
        if ($search) {
            $where = "(title LIKE :q OR author LIKE :q OR isbn LIKE :q OR category LIKE :q)";
            $params[':q'] = "%{$search}%";
        }
        return $this->paginateFilter($page, $perPage, $where, $params, "title ASC");
    }

    public function issues(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = "1=1";
        $params = [];
        if ($status) {
            $where = "li.status = :status";
            $params[':status'] = $status;
        }

        $sql = "
            SELECT li.*, b.title as book_title, b.isbn, s.name as student_name, s.admission_no
            FROM library_issues li
            JOIN library_books b ON b.id = li.book_id
            JOIN students s ON s.id = li.student_id
            WHERE {$where}
            ORDER BY li.issue_date DESC
            LIMIT :__limit OFFSET :__offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':__limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':__offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM library_issues li WHERE {$where}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public function issueBook(int $bookId, int $studentId, string $dueDate): bool
    {
        $book = $this->find($bookId);
        if (!$book || $book['available_copies'] <= 0) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO library_issues (book_id, student_id, issue_date, due_date, status)
                VALUES (?, ?, CURDATE(), ?, 'issued')
            ");
            $stmt->execute([$bookId, $studentId, $dueDate]);

            $upd = $this->db->prepare("UPDATE library_books SET available_copies = available_copies - 1 WHERE id = ?");
            $upd->execute([$bookId]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function returnBook(int $issueId): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM library_issues WHERE id = ? LIMIT 1");
        $stmt->execute([$issueId]);
        $issue = $stmt->fetch();
        if (!$issue || $issue['status'] === 'returned') {
            return false;
        }

        $dueDate = strtotime($issue['due_date']);
        $now = time();
        $fine = 0.00;
        if ($now > $dueDate) {
            $daysOverdue = (int) ceil(($now - $dueDate) / 86400);
            $fine = $daysOverdue * 1.00; // $1 per day
        }

        $this->db->beginTransaction();
        try {
            $updIssue = $this->db->prepare("
                UPDATE library_issues 
                SET status = 'returned', return_date = CURDATE(), fine_amount = ? 
                WHERE id = ?
            ");
            $updIssue->execute([$fine, $issueId]);

            $updBook = $this->db->prepare("UPDATE library_books SET available_copies = available_copies + 1 WHERE id = ?");
            $updBook->execute([$issue['book_id']]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
