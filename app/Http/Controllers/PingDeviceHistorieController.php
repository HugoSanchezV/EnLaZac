<?php

namespace App\Http\Controllers;

use App\Events\PingTecnicoEvent;
use App\Models\PingDeviceHistorie;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\PingDeviceHistorie\UpdatePingDeviceHistorieRequest;
use App\Models\Router;

class PingDeviceHistorieController extends Controller
{
    public function index(Request $request)
    {
        $query = PingDeviceHistorie::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('device_id', 'like', "%$search%")
                    ->orWhere('router_id', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('created_at','like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'desc');
        }

        $pingDevice = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'device_id' => $item->device_id,
                'router_id' => $item->router->ip_address,
                'address' => $item->device->address,
                'user_id' => $item->user->name ?? 'Sin asignar',
                'status' => $item->status,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $totalDeviceFail = PingDeviceHistorie::whereNotIn('status', [
            '1 paquete perdido',
            'Se han recibido todos lo paquetes exitosamente'
        ])->count();

        $totalPingDeviceCount = PingDeviceHistorie::count();
        $users = User::where('admin','3')->get();

       // dd($users);
        return Inertia::render('Admin/PingDeviceHistorie/PingDevice', [
            'pingDevice' => $pingDevice,
            'pagination' => [
                'links' => $pingDevice->links()->elements[0],
                'next_page_url' => $pingDevice->nextPageUrl(),
                'prev_page_url' => $pingDevice->previousPageUrl(),
                'per_page' => $pingDevice->perPage(),
                'total' => $pingDevice->total(),
            ],
            'success' => session('success') ?? null,
            'totalPingDeviceCount' => $totalPingDeviceCount,
            'totalDeviceFail' => $totalDeviceFail,
            'users' => $users

        ]);
    }


    public function index2(Request $request, Router $router)
    {
        //dd($router->id);
        $query = PingDeviceHistorie::query();
        $query->where('router_id',$router->id);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('device_id', 'like', "%$search%")
                    ->orWhere('router_id', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('created_at','like', "%$search%")
                    ;
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'desc');
        }

        $pingDevice = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'device_id' => $item->device_id,
                'router_id' => $item->router->ip_address,
                'address' => $item->device->address,
                'user_id' => $item->user->name ?? 'Sin asignar',
                'status' => $item->status,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $totalDeviceFail = PingDeviceHistorie::whereNotIn('status', [
            '1 paquete perdido',
            'Se han recibido todos lo paquetes exitosamente'
        ])->count();

        $totalPingDeviceCount = PingDeviceHistorie::count();
        $users = User::where('admin','3')->get();

       // dd($users);
        return Inertia::render('Admin/PingDeviceHistorie/PingDevice', [
            'pingDevice' => $pingDevice,
            'pagination' => [
                'links' => $pingDevice->links()->elements[0],
                'next_page_url' => $pingDevice->nextPageUrl(),
                'prev_page_url' => $pingDevice->previousPageUrl(),
                'per_page' => $pingDevice->perPage(),
                'total' => $pingDevice->total(),
            ],
            'success' => session('success') ?? null,
            'totalPingDeviceCount' => $totalPingDeviceCount,
            'totalDeviceFail' => $totalDeviceFail,
            'users' => $users

        ]);
    }

    public function create(PingDeviceHistorie $request)
    {   
        $validatedData = $request->validated();
        PingDeviceHistorie::create([
            'device_id' => $$validatedData['device_id'],
            'router_id' => $$validatedData['router_id'],
            'status' => $$validatedData['status'],
        ]);
        //sdd("termino de ingresar");
        //return redirect()->route('tickets')->with('success', 'Ticket creado con éxito');   
    }
    public function update(Request $request)
    {
        $validatedData = $request->validate(['user_id' => 'required|exists:users,id', ]);
        $pingDevice = PingDeviceHistorie::findOrFail($request->id);

        $pingDevice->user_id = $validatedData['user_id'];

        $pingDevice->save();
        self::make_user_notification($pingDevice);

        return redirect()->route('device.ping.historie')->with('success', 'El técnico ha sido notificado');
    }
    static function make_user_notification($ping){
        event(new PingTecnicoEvent($ping));
    }
    public function destroy($id)
    {
        $ping = PingDeviceHistorie::findOrFail($id);
        $ping->delete();

        return Redirect::route('device.ping.historie')->with('success', 'Ping Eliminado Con Éxito');
    }
}
