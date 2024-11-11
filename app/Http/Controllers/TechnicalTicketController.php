<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Events\TicketEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class TechnicalTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        $query->where('technical_id', Auth::id())->orWhere('user_id', Auth::id());
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

        return Inertia::render('Tecnico/Tickets/Tickets', [
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

        return Inertia::render('Tecnico/Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('admin', '=', '0')->get();

        return Inertia::render(
            'Tecnico/Tickets/Create',
            [
                'users' => $users,
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

        $validatedData = $request->validated();

        // print('HOLAAA');
        $ticket = Ticket::create([
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'user_id' => $user_id,
        ]);

        self::make_ticket_notification($ticket);

        return redirect()->route('technical.tickets')->with('success', 'Ticket creado con éxito');
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $name = $ticket->user->name; // Accede al nombre del usuario que creó el ticket

        return Inertia::render('Tecnico/Tickets/Edit', [
            'ticket' => $ticket,
            'nombre' => $name,
        ]);
    }


    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validatedData = $request->validated();
        $ticket->update($validatedData);
        return redirect()->route('technical.tickets')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function statusUpdate(UpdateTicketStatusRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->input('status');
        $ticket->save();


        return redirect()->route('technical.tickets')->with('success', 'Estado del Ticket Actualizado Con Éxito');
    }
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Redirect::route('technical.tickets')->with('success', 'Ticket Eliminado Con Éxito');
    }

    static function make_ticket_notification($ticket)
    {
        event(new TicketEvent($ticket));
    }
}
