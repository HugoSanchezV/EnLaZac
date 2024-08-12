<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TicketController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Ticket::query();

        //if ($request->type !== null && $request->type !== 'todos') {
        //    $query->where('admin', '=', $request->type);
       // }

        //$query->where('admin', '!=', 1);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('ubication', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $tickets = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'ubication' => $item->ubication,
                'status' => $item->status,
                'user_id' => $item->user_id,
            ];
        });
        /*
        $sedeCoordinates = $tickets -> map(function ($tickets){
            return $tickets -> ubication;
        }) -> toArray();

        $centerCoordinate = count($sedeCoordinates) > 0 ? $sedeCoordinates[0] : '';
        */
        
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


    public function create()
    {
        return Inertia::render('Coordi/Tickets/Create');
    }

    public function store(StoreTicketRequest $request)
    {
        $validatedData = $request->validated();
        $ticket = Ticket::create([
            'name' => $validatedData['name'],
            'alias' => $validatedData['alias'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'admin' => $validatedData['admin'],
        ]);

        return redirect()->route('usuarios')->with('success', 'Usuario creado con éxito', 'user');
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        return Inertia::render('Coordi/Tickets/Edit', [
            'ticket' => $ticket
        ]);
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $ticket->update($validatedData);
        return redirect()->route('usuarios')->with('success', 'Ticket Actualizado Con Éxito');
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Redirect::route('tickets')->with('success', 'Ticket Eliminado Con Éxito');
    }
}
