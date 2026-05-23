<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeAssigned extends Notification
{
    use Queueable;

    protected $gradeData;

    public function __construct(array $gradeData)
    {
        $this->gradeData = $gradeData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nilai Baru Diterima')
            ->line('Anda telah menerima nilai baru.')
            ->line('Mata Pelajaran: '.$this->gradeData['subject'] ?? 'N/A')
            ->line('Nilai: '.$this->gradeData['score'] ?? 'N/A')
            ->action('Lihat Nilai', url('/nilai'))
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return $this->gradeData;
    }
}
