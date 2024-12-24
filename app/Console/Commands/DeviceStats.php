<?php

namespace App\Console\Commands;

use App\Http\Controllers\DevicesController;
use App\Http\Controllers\PerformanceDeviceController;
use App\Models\Router;
use App\Models\PerformanceDevice;
use App\Services\RouterOSService;
use App\Services\TrafficService;
use Exception;
use Hamcrest\Core\HasToString;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;

class DeviceStats extends Command 
{
    //use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trafficData =  self::conectar();
        $performance = self::getData($trafficData);

        $service = new TrafficService;

        
        $controller = new PerformanceDeviceController();
       // $this->info($performance[1]);
       // $this->info("------------");
     //   $performance = PerformanceDevice::where('device_id', 16)->orderBy('created_at','asc')->get();
        //Log::info($performance);
        foreach ($performance as $p)
        {
             if(!is_null($p['device_id']))
             {
                //$this->info($p['device_id']);
       
            //Log::info("ddd");
                   // Log::info($p->id);
                    $service->createStats($controller->store($p));
                    // $service->createStats($p);

                
            }
        }
        
        
    }

    public function searchId($adress)
    {
        $ipOnly = strtok($adress, '/');

        $controller = new DevicesController();

        return $controller->searchID($ipOnly);


    }
    public function getData(array $trafficData)
    {
        $performance = [];

        // Verificar si el array está vacío
        if (empty($trafficData)) {
            return []; // Retorna un array vacío directamente si no hay datos
        }

        foreach ($trafficData as $data) {
            // Verificar si el dato no es nulo antes de procesarlo
            if (is_null($data)) {
                continue;
            }

            foreach ($data as $dt) {
                $perf = [];

                // Obtener la dirección y el ID del dispositivo
                $perf['address'] = $dt['target'];
                $perf['device_id'] = self::searchId($dt['target']);

                // Procesar la tasa (rate) si está presente y válida
                if (isset($dt['rate']) && strpos($dt['rate'], '/') !== false) {
                    $rateArray = explode('/', $dt['rate']);
                    $perf['rate'] = [
                        'upload' => self::convertToMB($rateArray[0]),
                        'download' => self::convertToMB($rateArray[1]),
                    ];
                }

                // Procesar los bytes si están presentes y válidos
                if (isset($dt['bytes']) && strpos($dt['bytes'], '/') !== false) {
                    $byteArray = explode('/', $dt['bytes']);
                    $perf['byte'] = [
                        'upload' => self::convertToMB($byteArray[0]),
                        'download' => self::convertToMB($byteArray[1]),
                    ];
                }

                // Agregar el rendimiento procesado al resultado
                $performance[] = $perf;
            }
        }

        return $performance;
    }

    public function conectar()
    {
        $trafficData = [];
        $routers = Router::cursor(); // Usa cursor para evitar consumir demasiada memoria

        foreach ($routers as $r) {
            try {
                $routerOSService = RouterOSService::getInstance();
                $trafficDat = $routerOSService->getQueueTraffic($r->id);

                if (!is_null($trafficDat) && is_array($trafficDat)) {
                    $trafficData[] = $trafficDat;
                }
            } catch (Exception $e) {
                Log::error("Error al conectar con el router {$r->id}: " . $e->getMessage());
            }
        }

        return $trafficData;
    }

    function convertToGb($bytes) {
        return round($bytes / 1024 / 1024 / 1024, 3);
    }
    function convertToMb($bytes) {
        return round($bytes / 1024 / 1024, 5); // Convertir a MB
    }

    function convertToMbps($bps) {
        return round($bps / 1000000, 5); // Convertir a Mbps
    }
}
