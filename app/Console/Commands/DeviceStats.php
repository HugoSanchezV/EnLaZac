<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Console\Command;

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
        self::getDate($trafficData);

        
    }
    public function getDate($trafficData)
    {
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
                        $targetTemp[] = $dt['target'];
                    
                        $rateArray = explode("/", $dt['rate']);
                        $byteArray = explode("/", $dt['bytes']);
                        // Asegurarse de que 'rate' contenga tanto subida como bajada
                        if (count($rateArray) === 2) {
                            $upload_rateTemp[] = self::convertToGb($rateArray[0]);  // Tasa de subida
                            $download_rateTemp[] = self::convertToGb($rateArray[1]);  // Tasa de bajada
                        }
                        if (count($byteArray) === 2) {
                            $upload_byteTemp[] = self::convertToGb($byteArray[0]);  // Tasa de subida
                            $download_byteTemp[] = self::convertToGb($byteArray[1]);  // Tasa de bajada
                        }
                    }
                    $target[] = $targetTemp;
                    $upload_rate[] = $upload_rateTemp;
                    $download_rate[] = $download_rateTemp;
                    $upload_byte[] = $upload_byteTemp;
                    $download_byte[] = $download_byteTemp;
                    
                    $targetTemp = [];
                    $upload_rateTemp = [];
                    $download_rateTemp = [];
                    $upload_byteTemp = [];
                    $download_byteTemp = [];
                }
            }  
        }
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
