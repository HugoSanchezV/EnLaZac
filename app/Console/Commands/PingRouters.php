<?php

namespace App\Console\Commands;

use App\Events\RouterDiagnosisEvent;
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
        $message = "";

        foreach($routers as $router)
        {
            $message = "Router: ".$router->ip_address."\n Estado: ".$message;
        }
      //  $this->info($message);
        self::enviarCorreo($message);
    }
    public function sendPing($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            "Dirección IP no válida: " . $ip . "\n";
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
                return "Todos los paquetes recibidos.\n";
               // return true;
              //  return true;
            } else if (strpos($pingResult, 'recibidos = 3') == true) {
                return "Se recibieron 3 paquetes.\n";
               // return false;
               // return false;
            }else if (strpos($pingResult, 'recibidos = 2') == true) {
                return "Se recibieron 2 paquetes.\n";
            }else if (strpos($pingResult, 'recibidos = 1') == true) {
                return "Se recibió 1 paquete.\n";

            }else{
                return "Perdida total de paquetes.\n";
            }
        } else {
            // Para Linux/macOS, verificar si no hay pérdida de paquetes
            if (strpos($pingResult, '0% packet loss') !== false) {
                return "El dispositivo está en línea.\n";
                //return true;
            } else {
               return  "El dispositivo no responde al ping.\n";
                //return false;
            }
        }

    }
    public function enviarCorreo($message){
        event(new RouterDiagnosisEvent($message));
    }
}
