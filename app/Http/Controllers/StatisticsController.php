<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Device;
use App\Models\PingDeviceHistorie;
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
    private $target = [];
    private $upload_rate = [];
    private $download_rate = [];
    private $upload_byte = [];
    private $download_byte = [];

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->admin == 0) {
            return self::showUser();
        }

        if ($user->admin == 1) {
            // dd($request->all());
            $id = isset($request->router) ? $request->router : null;
            return self::showAdmin($id);
        }

        if ($user->admin == 2) {
            return self::showCoordi();
        }

        if ($user->admin == 3) {
            return self::showTechnical();
        }
        return Inertia::render('DashboardBase');
    }

    public function showUser()
    {
        try {
            // dd(Auth::id());
            $user = User::with('device.inventorieDevice')->findOrFail(Auth::id());

            $devices = $user->device;

            $contracts = [];
            //dd($devices);
            foreach ($devices as $device) {
                // Verifica si el dispositivo tiene un contrato
                if (!is_null($device->inventorieDevice)) {

                    $contract = Contract::with('plan')->where('inv_device_id', $device->inventorieDevice->id)->get();
                    // $plan = $contract ? Plan::find($contract->plan_id) : null;

                    // Añadir los datos del dispositivo, contrato y plan al arreglo
                    $contracts[] = [
                        'device' => $device,
                        'contract' => $contract,
                        // 'plan' => $plan,
                    ];
                }
            }
            //dd("Termina el for");
            $mapKey = env('VITE_GOOGLE_MAPS_API_KEY');

            // dd($contracts);
            return Inertia::render('User/DashboardUser', [
                'user' => $user,
                // 'ticket' => Ticket::where('user_id', $id)->count(),
                'contracts' => $contracts,
                'mapKey' => $mapKey,
            ]);
        } catch (Exception $e) {
            return Redirect::route('usuarios')->with('error', 'Error al mostrar el usuario');
        }
    }

    public function showTechnical()
    {
        $user = Auth::user();
        $pings = PingDeviceHistorie::where('user_id', $user->id)->get();
        $routers = Router::count();
        $active_devices = Router::sum('enable_devices');

        return Inertia::render('Tecnico/DashboardTechnical', [
            'tickets' => sizeof($user->ticket),
            'pings' => sizeof($pings),
            'routers' => $routers,
            'active_devices' => $active_devices,
            'user' => $user,
        ]);
    }


    public function showCoordi()
    {
        $user = Auth::user();
        $users = User::where('admin', 0)->count();
        $contracts = Contract::count();
        $active_devices = Router::sum('enable_devices');

        return Inertia::render('Coordi/DashboardCoordi', [
            'tickets' => sizeof($user->ticket),
            'users' => $users,
            'contracts' => $contracts,
            'active_devices' => $active_devices,
            'user' => $user,
        ]);
    }

    public function showAdmin($id = null)
    {
        
        //Varias consultas para mandar aca
        $morrosos = self::morrososCount();

        $activeDevice = self::activeDevices();
        $activeContract = self::activeContract();
        $newTickets = self::currentTickets();
        $userCount = self::userCount();
        
        $id = isset($id) ? $id : (Router::first() ? Router::first()->id : null);

        if(!is_null($id)){
            $trafficData = self::conectar($id);
    
            self::gettingTraffic($trafficData);
        }

        $all_routers = Router::select('id')->where('sync', 1)->orderBy('id', 'asc')->get();
        return Inertia::render('DashboardBase', [
            'morrosos' => $morrosos,
            'activeDevice' => $activeDevice,
            'new_tickets' => $newTickets,
            'currentUsers' => $userCount,
            'activeContract' => $activeContract,
            'target' => $this->target,
            'upload_rate' => $this->upload_rate,
            'download_rate' => $this->download_rate,
            'upload_byte' => $this->upload_byte,
            'download_byte' => $this->download_byte,
            'routers' => $this->route,
            'all_routers' => $all_routers,
        ]);
    }

    private function gettingTraffic($trafficData)
    {
        if (!empty($trafficData)) {
            
            foreach ($trafficData as $data) {
                if (!is_null($data)) {
                    foreach ($data as $dt) {
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
                    $this->target[] = $targetTemp;
                    $this->upload_rate[] = $upload_rateTemp;
                    $this->download_rate[] = $download_rateTemp;
                    $this->upload_byte[] = $upload_byteTemp;
                    $this->download_byte[] = $download_byteTemp;

                    $targetTemp = [];
                    $upload_rateTemp = [];
                    $download_rateTemp = [];
                    $upload_byteTemp = [];
                    $download_byteTemp = [];
                }
            }
        }
    }
    function convertToGb($bytes)
    {
        return round($bytes / 1024 / 1024 / 1024, 3);
    }
    function convertToMb($bytes)
    {
        return round($bytes / 1024 / 1024, 5); // Convertir a MB
    }

    function convertToMbps($bps)
    {
        return round($bps / 1000000, 5); // Convertir a Mbps
    }
    public function conectar($id)
    {
        $trafficData = [];
        $routers = Router::where('id', $id)->get();

        //dd(($routers->count()));
        if (($routers->count()) != 0 && $routers[0]->sync == 1) {
            foreach ($routers as $r) {
                // dd($trafficData);
                try {

                    $routerOSService = RouterOSService::getInstance();
                    $trafficDat = $routerOSService->getQueueTraffic($r->id);
                    if (!is_null($trafficDat)) {
                        $trafficData[] = $trafficDat;
                        $this->route[] = $r->id;
                    }
                } catch (Exception $e) {
                    return Redirect::route('dashboard')->with('error', $e->getMessage());
                }
            }
            //dd($trafficData);
        } else {
            //dd("no router");
        }

        return $trafficData;
    }
    public function store() {}
    public function activeContract()
    {
        return (Contract::with('inventorieDevice.device.user')->where('active', '1')->get())->count();
    }

    public function userCount()
    {
        return User::where('admin','=','0')->count();
    }

    public function morrososCount()
    {
        return (Contract::with('inventorieDevice.device.user')->where('end_date', '<', Carbon::today())->where('active', false)->get())->count();
    }

    public function activeDevices()
    {
        return (Device::where('disabled', '0')->get())->count();
    }

    public function currentTickets()
    {
        $currentDate = Carbon::now()->format('Y-m-d');  // Obtener solo la fecha actual
        return (Ticket::with('user')->whereDate('created_at', $currentDate)->get())->count();
    }
}
