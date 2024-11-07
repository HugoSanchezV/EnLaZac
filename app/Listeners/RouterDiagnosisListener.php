<?php

namespace App\Listeners;

use App\Events\RouterDiagnosisEvent;
use App\Models\User;
use App\Notifications\RouterDiagnosisNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class RouterDiagnosisListener
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
    public function handle(RouterDiagnosisEvent $event): void
    {
        User::whereIn('admin', [1, 2, 3, 4])
        // Excluir al usuario que realizó la orden
        ->each(function(User $user) use ($event) {
            //$account = EmailAccount::all()->first();
            // Enviar notificación a los usuarios seleccionados
            Notification::send($user, new RouterDiagnosisNotification($event->message,  $account->fromAddress, $account->fromName));
        });
    }
}
