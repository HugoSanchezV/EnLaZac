<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InterestsController extends Controller
{
    public function index(Request $request)
    {
        $query = Interest::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $interest = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'amount' => $item->amount,
            ];
        });

        
        $totalInterestCount = Interest::count();

        return Inertia::render('Admin/Settings/Interest/Interest', [
            'interest' => $interest,
            'pagination' => [
                'links' => $interest->links()->elements[0],
                'next_page_url' => $interest->nextPageUrl(),
                'prev_page_url' => $interest->previousPageUrl(),
                'per_page' => $interest->perPage(),
                'total' => $interest->total(),
            ],
            'success' => session('success') ?? null,
            'totalInterestCount' => $totalInterestCount 
        ]);
    }

    public function getInterest($id){
        return Interest::findOrFail($id);
    }
}
