<?php

namespace App\Listeners;

use App\Events\RouterDiagnosisEvent;
use App\Models\User;
use App\Notifications\RouterDiagnosisNotification;
use Exception;
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
        try{
            User::whereIn('admin', [1, 3])
            ->each(function(User $user) use ($event) {
                Notification::send($user, new RouterDiagnosisNotification($event->message));
            });
        }catch(\Exception $e){
            throw new Exception('Error' . $e->getMessage());
        }
       
    }
}
