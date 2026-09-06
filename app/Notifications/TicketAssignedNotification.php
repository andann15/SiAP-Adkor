<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification 
{
    

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticketId = 'TKT-' . strtoupper(substr($this->ticket->id, 0, 8));

        return (new MailMessage)
            ->subject('Penugasan Tiket Baru: ' . $ticketId)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda telah ditugaskan untuk menangani sebuah tiket perbaikan baru.')
            ->line('**Nomor Tiket:** ' . $ticketId)
            ->line('**Aset:** ' . ($this->ticket->asset->name ?? '-'))
            ->line('**Prioritas:** ' . ($this->ticket->priority->name ?? '-'))
            ->action('Lihat Detail Tiket', route('tickets.show', $this->ticket->id))
            ->line('Mohon segera dicek dan ditindaklanjuti. Terima kasih!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

