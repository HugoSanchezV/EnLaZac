<?php

namespace App\Console\Commands;

use App\Http\Controllers\DevicesController;
use App\Http\Controllers\PerformanceDeviceController;
use App\Models\Router;
use App\Models\PerformanceDevice;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class DeviceStats extends Command
{
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
        
        $controller = new PerformanceDeviceController();
       // $this->info($performance[1]);
       // $this->info("------------");

        foreach ($performance as $p)
        {
            if(!is_null($p['device_id']))
            {
                $controller->store($p);
            }

         //   $this->info("++++++++++++++");

           // $controller->store($p);
            //self::store($p);
        }
        
    }

    public function searchId($adress)
    {
        $ipOnly = strtok($adress, '/');

        $controller = new DevicesController();

        return $controller->searchID($ipOnly);


    }
    public function getData($trafficData)
    {
        
        $performance = [];
        if(empty($trafficData)){
            $target = [];
            $upload_rate = [];
            $download_rate = [];
            $upload_byte = [];
            $download_byte = [];

        }else{
            foreach($trafficData as $data)
            {
                if(!is_null($data)){

                    foreach($data as $dt){
                        $perf = [];
                        //$this->info($dt['target']);
                      //  $this->info("data");
                    //    $this->info($dt);
                        //self::seachID($dt['target']);
                        $perf['address'] = $dt['target'];
                        $perf['device_id'] =  self::searchId($dt['target']);
                    
                        $rateArray = explode("/", $dt['rate']);
                        $byteArray = explode("/", $dt['bytes']);
                        // Asegurarse de que 'rate' contenga tanto subida como bajada
                        if (count($rateArray) === 2) {

                            $perf['rate'] = ['upload' => self::convertToGb($rateArray[0]),'download' => self::convertToGb($rateArray[1])]; 
                            // $upload_rateTemp = self::convertToGb($rateArray[0]);  // Tasa de subida
                            // $download_rateTemp = self::convertToGb($rateArray[1]);  // Tasa de bajada
                        }
                        if (count($byteArray) === 2) {
                            $perf['byte'] = ['upload' => self::convertToGb($byteArray[0]),'download' => self::convertToGb($byteArray[1])];
                        }
                        $performance[] = $perf;
                    }
                    
                }
            }  
        }
        return $performance;
    }
    public function conectar(){
        $trafficData = [];
        $routers = Router::all();
       //dd(($routers->count()));
        if(($routers->count()) != 0)
        {
            foreach($routers as $r)
            {
                try{   

                    $routerOSService = RouterOSService::getInstance();
                    $trafficDat = $routerOSService->getQueueTraffic($r->id);
                    if(!is_null($trafficDat)){
                        $trafficData[] = $trafficDat;
                    }
                } catch (Exception $e) {
                }
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
