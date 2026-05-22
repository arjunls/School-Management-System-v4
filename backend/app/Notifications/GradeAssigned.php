<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class GradeAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $subjectName,
        protected ?float $score,
        protected ?string $grade,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "Your {$this->subjectName} grade has been updated: " . ($this->score ?? 'N/A') . ($this->grade ? " ({$this->grade})" : ''),
            'subject' => $this->subjectName,
            'score' => $this->score,
            'grade' => $this->grade,
        ];
    }
}
