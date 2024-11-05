<?php

namespace App\Listeners;

use App\Events\RegisterUserEvent;
use App\Models\EmailAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\RegisterUserNotification;
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
        ->each(function(User $user) use ($event) {
            $account = EmailAccount::all()->first();
            // Enviar notificación a los usuarios seleccionados
            Notification::send($user, new RegisterUserNotification($event->user, $account->fromAddress, $account->fromName));
        });
    }
}
