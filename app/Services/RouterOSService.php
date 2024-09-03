<?php
namespace App\Services;

use App\Models\Router;
use App\Models\RouterosAPI as ModelsRouterosAPI;
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

        if (!$this->API->connect($ip, $user, $password)) {
            throw new \Exception("Hubo un error durante la conexión");
        }
    }

    public function executeCommand($command, $params = [])
    {
        return $this->API->comm($command, $params);
    }

    public function disconnect()
    {
        $this->API->disconnect();
    }
}
