<?php

namespace App\Services;

use App\Models\Router;
use App\Models\RouterosApi as ModelsRouterosAPI;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class RouterOSService
{
    private static $instance = null;
    private $API;

    private function __construct()
    {
        $this->API = new ModelsRouterosAPI();
        $this->API->debug(true);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function connect($routerId)
    {
        try {
            $router = Router::findOrFail($routerId);
            $ip = $router->ip_address;
            $user = $router->user;
            $password = Crypt::decrypt($router->password);
    
            if (!$this->API->connect($ip, $user, $password)) {
                // Obtén información sobre el último error
                $errorMessage = $this->API->error_str;
                $errorNumber = $this->API->error_no;
    
                throw new \Exception("Hubo un error durante la conexión: {$errorMessage} (Error no: {$errorNumber})");
            }
        } catch (Exception $e) {
            Log::error("Error al conectar al router: " . $e->getMessage());
            throw $e; // O manejarlo según necesites
        }
    }

    public function executeCommand($command, $params = [])
    {
        return $this->API->comm($command, $params);
    }

    public function connectMessage($routerId)
    {

        try{
            $router = Router::findOrFail($routerId);
            $ip = $router->ip_address;
            $user = $router->user;
            $password = Crypt::decrypt($router->password);
    
            // dd($ip. ": ".$user." : ".$password);
            if (!$this->API->connect($ip, $user, $password)) {
                //dd("Error");
                return false;
            }
            return true;
        }catch(Exception $e)
        {
            Log::Error($e->getMessage());
            return false;
            
        }
    }
    // Método para obtener el tráfico de las simple queues
    public function getQueueTraffic($routerId)
    {
        // Conectar al router MikroTik

        $connection = $this->connectMessage($routerId);
        if ($connection) {
            $trafficData = $this->executeCommand('/queue/simple/print');

            // Desconectar después de obtener los datos
            $this->disconnect();
        } else {
            return null;
        }
        // Retornar los datos de tráfico
        return $trafficData;

    }

    public function disconnect()
    {
        $this->API->disconnect();
    }
}
