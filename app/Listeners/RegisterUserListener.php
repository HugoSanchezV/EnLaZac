<?php

namespace App\Listeners;

use App\Events\RegisterUserEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class RegisterUserListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterUserEvent $event): void
    {
        User::whereIn('admin', [1, 2, 3, 4])
        // Excluir al usuario que realizó la orden
        ->where('id', '!=', $event->ticket->user_id)
        ->each(function(User $user) use ($event) {
            // Enviar notificación a los usuarios seleccionados
            Notification::send($user, new TicketNotification($event->ticket));
        });
    }
}
