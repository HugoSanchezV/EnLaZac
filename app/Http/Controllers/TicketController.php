<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketStatusRequest;
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
                    ->orWhere('subject', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('created_at','like', "%$search%");
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
                'subject' => $item->subject,
                'description' => $item->description,
                'status' => $item->status,
                'user_id' =>  $item->user_id,
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


    public function create()
    {
        return Inertia::render('Coordi/Tickets/Create');
    }

    public function store(StoreTicketRequest $request)
    {   $user_id = Auth::id();
        $validatedData = $request->validated();
        $ticket = Ticket::create([
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'user_id' => $user_id,
        ]);

        return redirect()->route('tickets')->with('success', 'Ticket creado con éxito');
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
        return redirect()->route('tickets')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function statusUpdate(UpdateTicketStatusRequest $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->input('status');
        $ticket->save();
        

        return redirect()->route('tickets')->with('success', 'Ticket Actualizado Con Éxito');
    }
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return Redirect::route('tickets')->with('success', 'Ticket Eliminado Con Éxito');
    }
    //Para Usuario
   
}
