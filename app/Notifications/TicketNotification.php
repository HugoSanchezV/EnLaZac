<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket; 

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                ->subject('Nuevo Ticket Generado')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('Se ha generado un nuevo ticket: ' . $this->ticket->subject)
                ->action('Ver Ticket', url('/tickets/show/' . $this->ticket->id))
                ->line('!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->ticket->id,
            'subject' =>  $this->ticket->subject,
            'created_at' => $this->ticket ->created_at,
            'user_id' => $this->ticket->user_id,
            'name' => $this->ticket->user->name
        ];
    }
}
