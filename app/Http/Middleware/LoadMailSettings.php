<?php

namespace App\Http\Middleware;

use App\Models\MailSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class LoadMailSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      //  dd("DSAD");
        // Obtener la configuración de correo desde la base de datos
        $mail = MailSetting::first();

        // Verificar si hay configuración de correo disponible
        if ($mail) {
            $data = [
                'driver' => $mail->transport,
                'host' => $mail->host,
                'port' => $mail->port,
                'encryption' => $mail->encryption,
                'username' => $mail->username,
                'password' => $mail->password,
                'from' => [
                    'address' => $mail->address,
                    'name' => $mail->name,
                ],
            ];

            // Configurar los ajustes de correo en tiempo de ejecución
            Config::set('mail', $data);
        }
        return $next($request);
    }
    
}
