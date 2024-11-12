<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\InterestsController;
use App\Models\Charge;
use App\Models\Contract;
use Illuminate\Support\Facades\Mail;
use DragonCode\Contracts\Queue\ShouldQueue;

class MailNotificationService implements ShouldQueue 
{
    public function sendNotification()
    {
        $htmlContent = "
            <h1>¡Bienvenido!</h1>
            <p>Gracias por unirte a nuestra plataforma. Esperamos que disfrutes de la experiencia.</p>
            <p>Saludos,<br>El equipo de Laravel</p>
        ";
    
        Mail::html($htmlContent, function ($message) {
            $message->to('l20030020@fresnillo.tecnm.mx')
                    ->subject('¡Bienvenido a nuestra plataforma!');
        });
    }
}
