<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCompletedNotification extends Notification
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
            ->subject('Tiket Selesai Dikerjakan: ' . $ticketId)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Tiket perbaikan Anda telah **selesai dikerjakan** oleh operator kami.')
            ->line('**Nomor Tiket:** ' . $ticketId)
            ->line('**Aset:** ' . ($this->ticket->asset->name ?? '-'))
            ->line('**Operator:** ' . ($this->ticket->assignedOperator->name ?? '-'))
            ->action('Lihat Hasil dan Tutup Tiket', route('tickets.show', $this->ticket->id))
            ->line('Silakan periksa hasil perbaikan dan klik "Tutup Tiket" untuk mengonfirmasi bahwa aset sudah berfungsi dengan baik. Terima kasih!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
