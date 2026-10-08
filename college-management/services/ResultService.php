<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Repositories\ResultRepository;
use Repositories\ActivityLogRepository;

final class ResultService
{
    public function __construct(
        private ResultRepository $results = new ResultRepository(),
        private ActivityLogRepository $logs = new ActivityLogRepository()
    ) {
    }

    /**
     * $entries = [ ['student_id' => 1, 'marks_obtained' => 78, 'max_marks' => 100], ... ]
     */
    public function publishBulk(int $examId, array $entries, ?int $actorId): int
    {
        Database::beginTransaction();
        try {
            $count = 0;
            foreach ($entries as $entry) {
                $this->results->create([
                    'exam_id' => $examId,
                    'student_id' => $entry['student_id'],
                    'marks_obtained' => $entry['marks_obtained'],
                    'max_marks' => $entry['max_marks'],
                    'grade' => $this->calculateGrade((float) $entry['marks_obtained'], (float) $entry['max_marks']),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $count++;
            }
            $this->logs->record($actorId, 'results_published', 'exams', $examId, null, ['count' => $count]);
            Database::commit();
            return $count;
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    private function calculateGrade(float $obtained, float $max): string
    {
        if ($max <= 0) {
            return 'N/A';
        }
        $percent = ($obtained / $max) * 100;
        return match (true) {
            $percent >= 90 => 'A+',
            $percent >= 80 => 'A',
            $percent >= 70 => 'B',
            $percent >= 60 => 'C',
            $percent >= 50 => 'D',
            default => 'F',
        };
    }
}
