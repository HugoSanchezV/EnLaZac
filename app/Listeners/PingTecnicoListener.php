<?php

namespace App\Listeners;

use App\Events\PingTecnicoEvent;
use App\Models\User;
use App\Notifications\PingTecnicoNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PingTecnicoListener
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
    public function handle(PingTecnicoEvent $event): void
    {
        $user = User::where('id', $event->ping->user_id)->get();

        Notification::send($user, new PingTecnicoNotification($event->ping));
    }
}
