<?php

namespace App\Listeners;

use App\Events\RouterDiagnosisEvent;
use App\Jobs\Mailing;
use App\Models\MailSetting;
use App\Models\User;
use App\Notifications\RouterDiagnosisNotification;
use Carbon\Carbon;
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
            $mail = MailSetting::first();

            User::whereIn('admin', [1, 3])
            ->each(function(User $user) use ($event, $mail) {

                if($mail){
                    Mailing::dispatch($mail,  $user, $this->createSubject(), $this->createHTML($event->message));
                }
                Notification::send($user, new RouterDiagnosisNotification($event->message));
            });
        }catch(\Exception $e){
            throw new Exception('Error' . $e->getMessage());
        }
       
    }

    public function createSubject(){
        return "Diagnostico de Routers: ".Carbon::now();
    }
    public function createHTML($message){
        $url = route('routers');
        return '
        <!DOCTYPE html>
        <html>
        <body style="background-color: #f7fafc; padding: 32px;">
            <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado empleado!</h1>
                <p style="color: #4a5568; margin-top: 16px;">
                    '.$message.'
                </p>
                <a href="'.$url.'" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                    Ir a routers
                </a>
            </div>
        </body>
        </html>';
    }
}
