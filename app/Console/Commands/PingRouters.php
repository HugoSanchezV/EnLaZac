<?php

namespace App\Console\Commands;

use App\Models\Router;
use Illuminate\Console\Command;

class PingRouters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ping-routers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle()
    {
        $routers = Router::all();

        foreach($routers as $router)
        {
            if(self::sendPing($router->ip_address))
            {
                self::enviarCorreo($router);
                
            }else{
                
            }
        }
    }
    public function sendPing($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $message = "Dirección IP no válida: " . $ip . "\n";
          //  return false;
        }
    
        // Determinar el comando 'ping' según el sistema operativo
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Comando para Windows
            $pingResult = shell_exec("ping -n 4 " . escapeshellarg($ip));
        } else {
            // Comando para Linux/macOS
            $pingResult = shell_exec("ping -c 4 " . escapeshellarg($ip));
        }
    
        // Verificar si el ping fue exitoso (depende del SO)
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Para Windows, verificar si se recibió el número completo de respuestas

        
            if (strpos($pingResult, 'recibidos = 4') == true) {
                $message = "El dispositivo está en línea.\n";
                return true;
              //  return true;
            } else {
                $message = "El dispositivo no responde al ping.\n";
                return false;
               // return false;
            }
        } else {
            // Para Linux/macOS, verificar si no hay pérdida de paquetes
            if (strpos($pingResult, '0% packet loss') !== false) {
                $message = "El dispositivo está en línea.\n";
                return true;
            } else {
                $message = "El dispositivo no responde al ping.\n";
                return false;
            }
        }

    }
    public function enviarCorreo(){

    }
}
