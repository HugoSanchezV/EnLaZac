<?php
namespace App\Services;

use App\Models\Router;
use App\Models\RouterosApi as ModelsRouterosAPI;
use Illuminate\Support\Facades\Crypt;

class RouterOSService
{
    private static $instance = null;
    private $API;

    private function __construct()
    {   
        $this->API = new ModelsRouterosAPI();
        $this->API->debug(false);
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
        
        $router = Router::findOrFail($routerId);
        $ip = $router->ip_address;
        $user = $router->user;
        $password = Crypt::decrypt($router->password);

       // dd($ip. ": ".$user." : ".$password);
        if (!$this->API->connect($ip, $user, $password)) {
            //dd("Error");
            throw new \Exception("Hubo un error durante la conexión");
            
        }
    }

    public function executeCommand($command, $params = [])
    {
        return $this->API->comm($command, $params);
    }

    // public function createQueue($routerId, $ipAddress)
    // {
    //     // Conectar al router MikroTik
    //     $this->connect($routerId);

    //     // Crear la cola simple para la IP especificada
    //     $this->executeCommand('/queue/simple/add', [
    //         'name' => 'Monitor_' . $ipAddress,
    //         'target' => $ipAddress . '/32',
    //         'max-limit' => '10M/10M' // Ajusta el límite de ancho de banda si es necesario
    //     ]);

    //     // Desconectar del router
    //     $this->disconnect();
        
    // }

    public function connectMessage($routerId)
    {
        
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
    }
    // Método para obtener el tráfico de las simple queues
    public function getQueueTraffic($routerId)
    {
        // Conectar al router MikroTik
        
      //  dd($routerId);
        $connection = $this->connectMessage($routerId);
        //dd($connection);
        if($connection){
            $trafficData = $this->executeCommand('/queue/simple/print');

            // Desconectar después de obtener los datos
            $this->disconnect();
    
            
            
        }else{
           // dd("nulo");
            return null;
        }
        // Retornar los datos de tráfico
        return $trafficData;
        // Obtener el tráfico de las colas simples
        
    }

    public function disconnect()
    {
        $this->API->disconnect();
    }
}
