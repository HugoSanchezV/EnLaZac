<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $contract;
    public $fromAddress;
    public $fromName;
    public function __construct(Contract $contract, $fromAddress, $fromName)
    {
        $this -> contract = $contract;
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->from($this->fromAddress, $this->fromName)
                    ->line('Se le notifica que en 2 días se termina su servicio de internet')
                    //Botón para enviar hacia el modulo de pagos online
                    ->action('Realizar pago online', url('/'))
                    ->line('Se le surgiere realizar su pago a tiempo para evitar el corte del servicio');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'end_date' => $this ->contract -> end_date,
        ];
    }
}
