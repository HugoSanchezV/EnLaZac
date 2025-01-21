<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use App\Events\TicketEvent;
use App\Events\TicketTechnicalEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TicketController extends Controller
{
    //
    protected $path = 'Coordi/Tickets';

    public function __construct()
    {
        if (Auth::user()->admin == 2) {
            $this->path = 'Coordi/Tickets_Coordi';
        }
    }

    public function index(Request $request)
    {
        $query = Ticket::query();

        $totalTicketsCount = 0;
        if (Auth::user()->admin == 2) {
            $query->where('user_id', Auth::id());
            $totalTicketsCount = Ticket::where('user_id', Auth::id())->count();
        } else {
            $totalTicketsCount = Ticket::count();
        }

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('subject', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
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

        if ($request->has('current') && $request->input('current') == 'true') {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfDay(),
            ]);
            // ->where('end_date', '<', Carbon::now());
        }

        $tickets = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'subject' => $item->subject,
                'description' => $item->description,
                'status' => $item->status,
                'user_id' =>  $item->user->name,
                'technical_id' =>  $item->technical->name ?? null,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });




        return Inertia::render($this->path . '/Tickets', [
            'tickets' => $tickets,
            'pagination' => [
                'links' => $tickets->links()->elements[0],
                'next_page_url' => $tickets->nextPageUrl(),
                'prev_page_url' => $tickets->previousPageUrl(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
            'success' => session('success') ?? null,
            'warning' => session('warning') ?? null,
            'totalTicketsCount' => $totalTicketsCount
        ]);
    }


    //Muestra la información del ticket y del usuario en específico
    public function show($id)
    {
        $ticket = Ticket::with('user')->findOrFail($id);

        return Inertia::render('Coordi/Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('admin', '=', '0')->get();
        $technicals = User::select('id', 'name')->where('admin', '=', '3')->get();

        return Inertia::render(
            'Coordi/Tickets/Create',
            [
                'users' => $users,
                'technicals' => $technicals,
            ]
        );
    }

    public function store(StoreTicketRequest $request)
    {
        if ($request->user_id == null) {
            $user_id = Auth::id();
        } else {
            $user_id = $request->user_id;
        }

        try{
            $validatedData = $request->validated();
    
            // print('HOLAAA');
            $ticket = Ticket::create([
                'subject' => $validatedData['subject'],
                'description' => $validatedData['description'],
                'user_id' => $user_id,
                'technical_id' => $request->technical_id ?? null,
            ]);
    
            self::make_ticket_notification($ticket);
            if(!is_null($request->technical_id)){
                self::make_ticket_technical_notification($ticket, $request->technical_id);
            }

        }catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->route('tickets')->with('error', 'Error al crear el Ticket');
        }

        return redirect()->route('tickets')->with('success', 'Ticket creado con éxito');
    }



    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $nombre = $ticket->user->name;

        $usersAndTechnicals = User::select('id', 'name', 'admin')
            ->whereIn('admin', [0, 3])
            ->get();

        $users = $usersAndTechnicals->where('admin', 0);
        $technicals = $usersAndTechnicals->where('admin', 3);

        return Inertia::render('Coordi/Tickets/Edit', [
            'ticket' => $ticket,
            'nombre' => $nombre,
            'users' => $users,
            'technicals' => $technicals,
        ]);
    }


    public function update(UpdateTicketRequest $request, $id)
    {

        $ticket = Ticket::findOrFail($id);

        try{
            $old_user = $ticket->technical_id;
            $validatedData = $request->validated();
            $ticket->update([
                'subject' => $validatedData['subject'],
                'status' => $validatedData['status'],
                'description' => $validatedData['description'],
                'user_id' => $request->user_id ?? null,
                'technical_id' => $request->technical_id ?? null,
            ]);
            if(!is_null($request->technical_id))
            {
                if($old_user != $request->technical_id){
                    self::make_ticket_technical_notification($ticket, $request->technical_id);
                }
            }

        }catch(Exception $e){
            Log::error($e->getMessage());
            return redirect()->route('tickets')->with('error', 'Error Al Actualizar El Ticket');
        }
        return redirect()->route('tickets')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function statusUpdate(UpdateTicketStatusRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->input('status');
        $ticket->save();


        return redirect()->route('tickets')->with('success', 'Estado del Ticket Actualizado Con Éxito');
    }
    public function destroy(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return Redirect::route('tickets', [
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])->with('success', 'Ticket Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('tickets', [
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])->with('error', 'Error al cargar el registro');
        }
    }

    static function make_ticket_technical_notification(Ticket $ticket, $id){
        event(new TicketTechnicalEvent($ticket, $id));
    }
    static function make_ticket_notification($ticket)
    {
        event(new TicketEvent($ticket));
    }
    //Para Usuarios mortales--------------------------------------------------------------------------

    public function index_user(Request $request)
    {
        $userId = Auth::id(); // Obtén el ID del usuario autenticado
        Log::info(Auth::id());
        $query = Ticket::query();

        // Filtra los tickets por el usuario autenticado
        $query->where('user_id', $userId);

        // Filtro de búsqueda
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('subject', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        // Ordenamiento
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        // Paginación y transformación de los datos
        $tickets = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'subject' => $item->subject,
                'description' => $item->description,
                'status' => $item->status,
                //               'user_id' =>  $item->user_id,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $totalTicketsCount = Ticket::where('user_id', $userId)->count(); // Cuenta total de tickets del usuario autenticado

        return Inertia::render('User/Tickets/Tickets', [
            'tickets' => $tickets,
            'pagination' => [
                'links' => $tickets->links()->elements[0],
                'next_page_url' => $tickets->nextPageUrl(),
                'prev_page_url' => $tickets->previousPageUrl(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalTicketsCount' => $totalTicketsCount
        ]);
    }
    public function update_user(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validatedData = $request->validated();
        $ticket->update($validatedData);
        return redirect()->route('tickets.usuario')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function store_user(StoreTicketRequest $request)
    {
        $validatedData = $request->validated();

        $ticket = Ticket::create([
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'user_id' => Auth::id(),
        ]);

        self::make_ticket_notification($ticket);

        return redirect()->route('tickets.usuario')->with('success', 'Ticket creado con éxito');
    }
    public function edit_user($id)
    {
        $ticket = Ticket::findOrFail($id);
        $name = $ticket->user->name; // Accede al nombre del usuario que creó el ticket

        return Inertia::render('User/Tickets/Edit', [
            'ticket' => $ticket,
            'nombre' => $name,
        ]);
    }
    public function destroy_user($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Redirect::route('tickets.usuario')->with('success', 'Ticket Eliminado Con Éxito');
    }
    public function create_user()
    {
        return Inertia::render('User/Tickets/Create');
    }

    static function user_id()
    {
        return Auth::id();
    }
}
