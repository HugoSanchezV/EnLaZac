<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\RegisterUserEvent;
use App\Exports\GenericExport;
use App\Imports\UserImport;
use App\Models\Contract;
use App\Models\Plan;
use App\Models\PreRegisterUser;
use App\Models\Ticket;
use App\Models\TelegramAccount;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\UserTelegramService;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    //
    protected $telegramService;
    protected $path = 'Admin';

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;

        if (Auth::user()->admin == 2) {
            $this->path = 'Coordi';
        }
    }

    public function index(Request $request)
    {
        $query = User::query();

        if (Auth::user()->admin == 2) {
            $query->where('admin', 0);
        }

        if ($request->type !== null && $request->type !== 'todos') {
            $query->where('admin', '=', $request->type);
        }

        $query->where('admin', '!=', 1);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('alias', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        // if ($request->attribute) {
        //     $query->orderBy($request->attribute, $request->order);
        // } else {
        //     $query->orderBy('id', 'asc');
        // }
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $users = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'alias' => $item->alias ?? "Sin asignar",
                'email' => $item->email,
                'phone' => $item->phone,
                'role' => $item->admin,
            ];
        });

        $totalUsersCount = User::where('admin', '!=', 1)->count();

        return Inertia::render($this->path . '/Users/Usuarios', [
            'users' => $users,
            'pagination' => [
                'links' => $users->links()->elements[0],
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalUsersCount' => $totalUsersCount
        ]);
    }


    public function create()
    {
        return Inertia::render($this->path . '/Users/Create', [
            'user' => Auth::user(),
        ]);
    }
    public function show($id)
    {
        try {
            $user = User::with('device.inventorieDevice')->findOrFail($id);

            $devices = $user->device;

            $contracts = [];

            foreach ($devices as $device) {
                // Verifica si el dispositivo tiene un contrato
                $contract = Contract::with('plan')->where('inv_device_id', $device->inventorieDevice->id)->get();
                // $plan = $contract ? Plan::find($contract->plan_id) : null;

                // Añadir los datos del dispositivo, contrato y plan al arreglo
                $contracts[] = [
                    'device' => $device,
                    'contract' => $contract,
                    // 'plan' => $plan,
                ];
            }

            // dd($contracts);
            return Inertia::render('Admin/Users/Show', [
                'user' => $user,
                // 'ticket' => Ticket::where('user_id', $id)->count(),
                'contracts' => $contracts
            ]);
        } catch (Exception $e) {
            dd($e->getMessage());
            return Redirect::route('usuarios')->with('error', 'Error al mostrar el usuario');
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                $user = User::create([
                    'name' => $validatedData['name'],
                    'alias' => $validatedData['alias'],
                    'email' => $validatedData['email'],
                    'phone' => $validatedData['phone'],
                    'password' => Hash::make($validatedData['password']),
                    'admin' => $validatedData['admin'],
                ]);

                $register = PreRegisterUser::where('phone', $validatedData['phone'])->first();

                if ($register) {
                    $register->delete();
                }

                self::make_register_notification($user);

                return $user;
            });

            $chatId = UserTelegramService::createContactTelegramSendMessage([
                'name' => $request->name,
                'alias' => $request->alias,
                'phone' => '52' . $request->phone,
            ], $this->telegramService);

            if ($chatId) {
                TelegramAccount::create([
                    'chat_id' => $chatId,
                    'user_id' => $user->id,
                ]);
            }
            $message = isset($chatId) ? 'Agregado a telegram' : 'Sin telegram';

            return redirect()->route('usuarios')->with('success', 'Usuario creado con éxito ' . $message, 'user');
        } catch (Exception $e) {
            return redirect()->route('usuarios')->with('success', 'Hubo un problema al crear el registro', 'user');
        }
    }

    public function createContactTelegram($id)
    {
        try {
            $user = User::findOrFail($id);
            $result = UserTelegramService::createContactTelegramSendMessage([
                "name" => $user->name,
                "alias" => $user->alias ?? $user->id,
                "phone" => $user->phone,
            ], $this->telegramService);

            if ($result) {
                return redirect()->route('usuarios.show', $user->id)->with('success', 'Se ha agregado el usurio correctamente');
            } else {
                return redirect()->route('usuarios.show', $user->id)->with('error', 'El usuario no tiene telegram o no se encutra disposnible');
            }
        } catch (Exception $e) {
            return redirect()->route('usuarios.show', $user->id)->with('error', 'Hubo un problema al agregar el contacto');
        }
    }

    static function make_register_notification($user)
    {
        event(new RegisterUserEvent($user));
    }
    public function edit($id)
    {
        try {

            $user = User::findOrFail($id);
            return Inertia::render($this->path . '/Users/Edit', [
                'user' => $user
            ]);
        } catch (Exception $e) {
            return Redirect::route('usuarios')->with('error', 'Error al mostrar el registro del usuarios');
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $user = User::findOrFail($id);

                $validatedData = $request->validated();

                if (!empty($validatedData['password'])) {
                    $validatedData['password'] = Hash::make($validatedData['password']);
                } else {
                    unset($validatedData['password']);
                }

                $user->update($validatedData);

                $register = PreRegisterUser::where('phone', $validatedData['phone'])->first();

                if ($register) {
                    $register->delete();
                }
            });
            return Redirect::route('usuarios')->with('success', 'Usuario Actualizado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('usuarios')->with('error', 'Error al momento de actualizar registro');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);

                $telegram = $user->telegramAccount;

                $result = false;
                if ($telegram) {
                    $result = UserTelegramService::destroyContact($this->telegramService, $telegram->chat_id);
                }
                if (!$result && isset($telegram)) {
                    throw new Exception('El contacto en telegram no se ha podido eliminar, vuleve intentar', 0);
                }

                $user->delete();
            });
            return Redirect::route('usuarios', $request->query())->with('success', 'Usuario Eliminado Con Éxito ');
        } catch (\Exception $e) {
            if ($e->getCode() === 0) {
                return Redirect::route('usuarios', $request->query())->with('error', $e->getMessage());
            } else {
                return Redirect::route('usuarios', $request->query())->with('error', 'Ocurrio un error con el registro');
            }
        }
    }

    public function exportExcel()
    {
        $query = User::query()
            ->where('admin', '!=', 1)
            ->select('id', 'name', 'alias', 'email', 'phone', 'admin');

        $headings = [
            'ID',
            'Nombre',
            'Alias',
            'Email',
            'Número',
            'Rol',
        ];

        $mappingCallback = function ($user) {
            return [
                $user->id,
                $user->name,
                $user->alias ?? 'Sin asignar',
                $user->email,
                $user->phone,
                UserService::getTypeUser($user->admin)
            ];
        };

        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'usuarios.xlsx');
    }

    public function importExcel(Request $request)
    {
        try {
            $file = $request->excel;
            Excel::import(new UserImport, $file);
            return Redirect::route('usuarios')->with('success', 'Archivo Importado Con Éxito ');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $rows = $failure->row(); // Fila donde ocurrió el error
                // $attribute = $failure->attribute(); // Nombre del campo con error
                // $errors = $failure->errors(); // Lista de errores para este campo
                // $values = $failure->values(); // Valores originales de esa fila

                // Aquí puedes hacer algo como registrar los errores, mostrarlos al usuario, etc.
                // Por ejemplo, podrías registrar los errores en una variable de sesión o en un log

                return redirect()->back()->with($rows);
            }
        } catch (Exception $e) {
            return Redirect::route('usuarios')->with('error', 'Error al Importar ' . $e->getMessage());
        }
    }


    public function messageTelegram($id)
    {
        try {
            $user = User::with('telegramAccount')->findOrFail($id);
            return Inertia::render('Admin/Users/TelegramMessage', [
                "user" => $user,
                'success' => session('success') ?? null,
                'error' => session('error') ?? null,
            ]);
        } catch (Exception $e) {
            return Redirect::route('usuarios')->with('error', 'Error al cargar el registro ');
        }
    }

    public function sendMessageTelegram(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'chat_id' => 'required|numeric|exists:telegram_accounts,chat_id',
                'message' => 'required|string|max:500|min:4',
            ]);

            $result = UserTelegramService::sendMessage($this->telegramService, $validatedData["chat_id"], $validatedData["message"]);
            if ($result) {
                return redirect()->route('usuarios.show', $request->user_id)->with('success', 'Se ha enviado el mensaje correctamente');
            }
        } catch (Exception $e) {
            return redirect()->route('usuarios.show', $request->user_id)->with('error', 'Error al envair mensaje');
        }
    }
}
