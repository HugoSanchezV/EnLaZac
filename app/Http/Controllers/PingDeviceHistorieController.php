<?php

namespace App\Http\Controllers;

use App\Events\PingTecnicoEvent;
use App\Models\PingDeviceHistorie;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Router;
use Exception;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class PingDeviceHistorieController extends Controller
{
    public function index(Request $request, $url = 'Admin/PingDeviceHistorie/PingDevice')
    {
        $query = PingDeviceHistorie::query();

        if (Auth::user()->admin === 3) {
            $query->where('user_id', Auth::id());
            $url = 'Tecnico/PingDeviceHistorie/PingDevice';
        }

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('router', function ($q) use ($search) {
                        $q->where('ip_address', 'like', "%$search%");
                    })
                    ->orWhereHas('device', function ($q) use ($search) {
                        $q->where('address', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }

        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


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
        $users = User::where('admin', '3')->get();

        // dd($users);
        return Inertia::render($url, [
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
        $query->where('router_id', $router->id);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('device', function ($q) use ($search) {
                        $q->where('address', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('created_at', 'like', "%$search%")
                ;
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

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
        $users = User::where('admin', '3')->get();

        // dd($users);
        return Inertia::render('Admin/PingDeviceHistorie/PingDevice', [
            'pingDevice' => $pingDevice,
            'router' => $router ?? null,
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

    public function index_technical(Request $request, Router $router)
    {
        //dd($router->id);
        $query = PingDeviceHistorie::query();
        $query->where('router_id', $router->id);
        $query->where('user_id', Auth::id());

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('device', function ($q) use ($search) {
                        $q->where('address', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('created_at', 'like', "%$search%")
                ;
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

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
        $users = User::where('admin', '3')->get();

        // dd($users);
        return Inertia::render('Tecnico/PingDeviceHistorie/PingDevice', [
            'pingDevice' => $pingDevice,
            'router' => $router ?? null,
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
        try {
            // $validatedData = $request->validated();
            //   dd($request->id);
            $validatedData = $request->validate(['user_id' => 'required|exists:users,id',]);
            $pingDevice = PingDeviceHistorie::findOrFail($request->id);
            $pingDevice->user_id = $validatedData['user_id'];


            $pingDevice->save();
            self::make_user_notification($pingDevice);

            self::redirectDecition($request, 'success', 'El técnico ha sido notificado');
        } catch (Exception $e) {
            self::redirectDecition($request, 'error', 'Error al momento de cargar el registro');
        }
    }

    static function make_user_notification($ping)
    {
        event(new PingTecnicoEvent($ping));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $ping = PingDeviceHistorie::findOrFail($id);
            $ping->delete();
            self::redirectDecition($request, 'success', 'Ping Eliminado Con Éxito');
        } catch (Exception $e) {
            dd($e->getMessage());
            self::redirectDecition($request, 'error', 'Error al cargar el registro');
        }
    }


    public function redirectDecition(Request $request, String $type, String $message)
    {
        $url = Auth::user()->admin === 3 ? 'technical.device.ping.historie' : 'device.ping.historie';

        if ($request->router) {
            $url = Auth::user()->admin === 3 ? 'technical.router.device.ping.historie' : 'router.device.ping.historie';
            return redirect()->route($url, [
                "router" => $request->router ?? null,
                "q" => $request->q ?? null,
                "attribute" => $request->attribute ?? null,
                "order" => $request->order ?? null,
            ])->with($type, $message);
        }
        return redirect()->route($url, [
            "q" => $request->q,
            "attribute" => $request->attribute,
            "order" => $request->order,
        ])->with($type, $message);
    }
}
