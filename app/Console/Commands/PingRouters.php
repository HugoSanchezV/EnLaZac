<?php

namespace App\Console\Commands;

use App\Events\RouterDiagnosisEvent;
use App\Http\Controllers\PingController;
use App\Jobs\SendMessageErrorToClientJob;
use App\Models\Ping;
use App\Models\Router;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
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

        foreach ($routers as $router) {

            $message = $message . "Router: " . $router->ip_address . "\n Estado: " . self::sendPing($router->ip_address, $router->id);
        }
        self::enviarCorreo($message);
        UserTelegramService::sendMessageToAdmin(new TelegramService(), $message);
    }
    public function sendPing($ip, $id)
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
                $message = "Todos los paquetes recibidos.\n";
                self::ping_register($message, $id);
                return $message;
                // return true;
                //  return true;
            } else if (strpos($pingResult, 'recibidos = 3') == true) {
                $message = "Se recibieron 3 paquetes.\n";
                self::ping_register($message, $id);
                return $message;
                // return false;
                // return false;
            } else if (strpos($pingResult, 'recibidos = 2') == true) {
                $message = "Se recibieron 2 paquetes.\n";
                self::ping_register($message, $id);
                return $message;
            } else if (strpos($pingResult, 'recibidos = 1') == true) {
                $message = "Se recibió 1 paquete.\n";
                self::ping_register($message, $id);
                return $message;
            } else {
                $message = "Perdida total de paquetes.\n";
                self::ping_register($message, $id);
                return $message;
            }
            self::ping_register($message, $id);
            return $pingResult;
        } else {
            // Para Linux/macOS, verificar si no hay pérdida de paquetes
            if (strpos($pingResult, '0% packet loss') !== false) {
                $message = "El dispositivo está en línea.\n";
                self::ping_register($message, $id);
                return $message;
                //return true;
            } else {
                $message =  "El dispositivo no responde al ping.\n";
                self::ping_register($message, $id); 
                
                SendMessageErrorToClientJob::dispatch($id);

                return $message;
                //return false;
            }
            self::ping_register($message, $id);
            return $pingResult;
        }
    }
    public function ping_register($content, $id)
    {
        $ping = new Ping();
        $controlador = new PingController();

        $ping->content = $content;
        $ping->router_id = $id;

        $controlador->store($ping);
    }
    public function enviarCorreo($message)
    {
        event(new RouterDiagnosisEvent($message));
    }
}
