<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RegisterUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $user;
    public $fromAddress;
    public $fromName;
    public function __construct(User $user, $fromAddress, $fromName)
    {
        $this -> user = $user;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
        //return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                ->from($this->fromAddress, $this->fromName)
                ->subject('Nuevo Usuario Registrado')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('Han creado un usuario nuevo: ' . $this->user->name)
                ->line('Con el ID: '.$this->user->id)
                ->action('Ver Usuario', url('/usuarios/show/' . $this->user->id))
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
            'id' => $this->user->id,
            'name' =>  $this->user->name,
            'created_at' => $this->user ->created_at,
        ];
    }
}
