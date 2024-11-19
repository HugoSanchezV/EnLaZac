<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Device;
use App\Models\Router;
use App\Models\Ticket;
use App\Models\User;
use App\Services\RouterOSService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Redirect;

class StatisticsController extends Controller
{
    private $route = [];

    public function index()
    {
        if(Auth::user()->admin == 1){
            return self::showAdmin();
        }else{
            return Inertia::render('DashboardBase');
        }
    }
    public function showAdmin()
    {
      //  dd(Carbon::now());
        //Varias consultas para mandar aca
        $morrosos = self::morrososCount();
     //  dd($morrosos);
        $activeDevice = self::activeDevices();
        $activeContract = self::activeContract();
        $newTickets = self::currentTickets();
        $userCount = self::userCount();

        $trafficData = self::conectar();
     //   dd($trafficData);
        if(empty($trafficData)){
            $target = [];
            $upload_rate = [];
            $download_rate = [];
            $upload_byte = [];
            $download_byte = [];

            $this->route = [];

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
                            $upload_rateTemp[] = self::convertToMb($rateArray[0]);  // Tasa de subida
                            $download_rateTemp[] = self::convertToMb($rateArray[1]);  // Tasa de bajada
                        }
                        if (count($byteArray) === 2) {
                            $upload_byteTemp[] = self::convertToMb($byteArray[0]);  // Tasa de subida
                            $download_byteTemp[] = self::convertToMb($byteArray[1]);  // Tasa de bajada
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
  //     dd(Carbon::now()->toString());

      //  dd($userCount->count());
     // dd('Terminó');
        return Inertia::render('DashboardBase',[
            'morrosos' => $morrosos,
            'activeDevice' => $activeDevice,
            'new_tickets' =>$newTickets,
            'currentUsers' => $userCount,
            'activeContract' => $activeContract,
            'target' => $target,
            'upload_rate' =>$upload_rate,
            'download_rate' =>$download_rate,
            'upload_byte' =>$upload_byte,
            'download_byte' =>$download_byte,
            'routers' => $this->route,
            
        ]);
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
    public function conectar(){
        $trafficData = [];
        $routers = Router::all();
       //dd(($routers->count()));
        if(($routers->count()) != 0)
        {
            foreach($routers as $r)
            {
               // dd($trafficData);
                try{   

                    $routerOSService = RouterOSService::getInstance();
                    $trafficDat = $routerOSService->getQueueTraffic($r->id);
                    if(!is_null($trafficDat)){
                        $trafficData[] = $trafficDat;
                        $this->route [] = $r->id;
                    }
                    
                } catch (Exception $e) {
                    return Redirect::route('dashboard')->with('error', $e->getMessage());
                }
                
            }
           // dd("holas");
        }else{
           // dd("no router");
        }
        
        return $trafficData;
    }
    public function store(){}
    public function activeContract()
    {return Contract::where('active','1')->get();}
    
    public function userCount()
    {return User::all();}

    public function morrososCount()
    {return (Contract::with('device.device.user')->where('end_date','<', Carbon::today())->where('active',false)->get());}

    public function activeDevices()
    {return (Device::where('disabled','0')->get());}

    public function currentTickets()
    {$currentDate = Carbon::now()->format('Y-m-d');  // Obtener solo la fecha actual
     return Ticket::with('user')->whereDate('created_at', $currentDate)->get();}
}
