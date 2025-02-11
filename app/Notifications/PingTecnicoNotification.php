<?php

namespace App\Notifications;

use App\Models\PingDeviceHistorie;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PingTecnicoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $ping;

    public function __construct(PingDeviceHistorie $ping)
    {
        $this->ping = $ping;

      //  dd($this->fromAddress);
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
        //dd($this->fromAddress);
        return (new MailMessage)
                   // ->from($this->fromAddress, $this->fromName)
                    ->line('Realizar revisión a siguiente dispositivo: '.$this->ping->id)
                    ->action('Ver dispositivo', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'device_id' => $this->ping->device_id,
            'router_id' => $this->ping->router_id,
        ];
    }
}
