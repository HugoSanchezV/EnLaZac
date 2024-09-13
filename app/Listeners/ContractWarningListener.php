<?php

namespace App\Listeners;

use App\Events\ContractWarningEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\ContractWarningNotification;

class ContractWarningListener
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
    public function handle(ContractWarningEvent  $event): void
    {
     //   dd("Eh puto");
        $user = User::where('id', '=', $event->contract->user_id)->get();
        //Verifica si existe el usuario
        Notification::send($user, new ContractWarningNotification($event->contract));
    
        
       
    }
}
