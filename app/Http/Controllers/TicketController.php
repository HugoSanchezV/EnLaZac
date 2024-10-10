<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use App\Events\TicketEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Event;
use Inertia\Inertia;

class TicketController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('subject', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('created_at','like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $tickets = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'subject' => $item->subject,
                'description' => $item->description,
                'status' => $item->status,
                'user_id' =>  $item->user->name,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        
        $totalTicketsCount = Ticket::count();

        return Inertia::render('Coordi/Tickets/Tickets', [
            'tickets' => $tickets,
            'pagination' => [
                'links' => $tickets->links()->elements[0],
                'next_page_url' => $tickets->nextPageUrl(),
                'prev_page_url' => $tickets->previousPageUrl(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
            'success' => session('success') ?? null,
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
        
        return Inertia::render(
            'Coordi/Tickets/Create',
            [
                'users' => $users,
            ]
        );
    }

    public function store(StoreTicketRequest $request)
    {
        if($request->user_id == null){
            $user_id = Auth::id();
        }else{
            $user_id = $request->user_id;
        }
        
        $validatedData = $request->validated();

       // print('HOLAAA');
        $ticket = Ticket::create([
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'user_id' => $user_id,
        ]);

       self::make_ticket_notification($ticket);

        return redirect()->route('tickets')->with('success', 'Ticket creado con éxito');    
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $name = $ticket->user->name; // Accede al nombre del usuario que creó el ticket

        return Inertia::render('Coordi/Tickets/Edit', [
            'ticket' => $ticket,
            'nombre' => $name,
        ]);
    }

    
    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validatedData = $request->validated();
        $ticket->update($validatedData);
        return redirect()->route('tickets')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function statusUpdate(UpdateTicketStatusRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->input('status');
        $ticket->save();
        

        return redirect()->route('tickets')->with('success', 'Estado del Ticket Actualizado Con Éxito');
    }
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Redirect::route('tickets')->with('success', 'Ticket Eliminado Con Éxito');
    }

    static function make_ticket_notification($ticket){
        event(new TicketEvent($ticket));
    }
    //Para Usuarios mortales--------------------------------------------------------------------------

    public function index_user(Request $request)
    {
        $userId = Auth::id(); // Obtén el ID del usuario autenticado
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
                    ->orWhere('created_at','like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }
    
        // Ordenamiento
        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }
    
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
            'totalTicketsCount' => $totalTicketsCount 
        ]);
    }
   
}
