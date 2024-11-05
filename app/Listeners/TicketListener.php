<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\Notification;
use App\Events\TicketEvent;
use App\Models\EmailAccount;
use Illuminate\Support\Facades\Log;

class TicketListener
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
    public function handle(TicketEvent $event): void
    {
        //$account = EmailAccount::all()->first();
        User::whereIn('admin', [1, 2, 3, 4])
        // Excluir al usuario que realizó la orden
        ->where('id', '!=', $event->ticket->user_id)
        ->each(function(User $user) use ($event) {
            // Enviar notificación a los usuarios seleccionados
            $account = EmailAccount::all()->first();
            Notification::send($user, new TicketNotification($event->ticket, $account->fromAddress, $account->fromName));
        });
    }
}
