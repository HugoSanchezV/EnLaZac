<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExtraChange\StoreChargeRequest;
use Inertia\Inertia;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::query();

        //if ($request->type !== null && $request->type !== 'todos') {
        //    $query->where('admin', '=', $request->type);
       // }

        //$query->where('admin', '!=', 1);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('paid', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $charges = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'contract_id' => $item->subject,
                'description' => $item->description,
                'amount' => $item->status,
                'paid' =>  $item->user_id,
            ];
        });

        
        $totalChargesCount = Charge::count();

        return Inertia::render('Coordi/Tickets/Tickets', [
            'charges' => $charges,
            'pagination' => [
                'links' => $charges->links()->elements[0],
                'next_page_url' => $charges->nextPageUrl(),
                'prev_page_url' => $charges->previousPageUrl(),
                'per_page' => $charges->perPage(),
                'total' => $charges->total(),
            ],
            'success' => session('success') ?? null,
            'totalChargesCount' => $totalChargesCount 
        ]);
    }

    public function store(Charge $request)
    {   
        $charge = Charge::create([
            'contract_id' => $request->contract_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'paid' => $request->paid,
            
        ]);
       // print('Cargo creado');
       // return redirect()->route('')->with('success', 'Ticket creado con éxito');
    }
}
