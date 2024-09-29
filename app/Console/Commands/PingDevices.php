<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Router;
use App\Services\RouterOSService;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PingDevices extends Command
{
    protected $signature = 'app:ping-devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping a todos los dispositivos cada hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $router = Router::select('id', 'ip_address','user') -> get();

        $device = Device::select('id','router_id', 'address', 'disabled') ->get();

        $array = [];
        
        foreach($router as $r)
        {
            self::routerDevice($r, $device);
        }
    }
    public function routerDevice($r, $device)
    {
       $disableDevice = 0;
        foreach($device as $d)
        {
           // $this->info($r->id." =  ".$d -> router_id);
            if($r->id == $d -> router_id)
            {
               // $this->info('DESACTIVADO: '.$d->disabled);
                if($d->disabled == 1)
                {
                    self::pingDevice($r ,$d->address); 
                }else{
                    $disableDevice++;
                }
               
            }
            
        }
        print("Dispositivos desconectados: ".$disableDevice);
        return $disableDevice;

        
    }
    private function pingDevice($r, $ip)
    {
        // Ejecutar el comando ping
        $API = RouterOSService::getInstance();

        $API->connect($r->id);
        
        $params = [
                'address' => $ip,  // Dirección IP del dispositivo al que deseas hacer ping
                'count' => '4'     // Número de paquetes a enviar
            ];

            $result = $API->executeCommand('/ping', $params);
      
            foreach ($result as $ping) {
                $this->info($ping['host']);
                if(isset($ping['status']))
                {
                    $this->info($ping['status']);

                }else{
                    $this->info("Correcto ping");
                }
            }
            $API->disconnect();
    }
}
                