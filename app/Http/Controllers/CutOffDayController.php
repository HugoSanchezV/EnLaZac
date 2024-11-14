<?php

namespace App\Http\Controllers;

use App\Models\CutOffDay;
use Illuminate\Http\Request;

class CutOffDayController extends Controller
{
    public function index(){
        $cutOffDay = CutOffDay::first()->day ?? null;
    }
    public function update(Request $request){
        $validated = $request->validate([
            'day' => 'required|integer|min:1|max:31', // Validar que sea un día válido
        ]);
        CutOffDay::updateOrCreate([], ['day' => $validated['day']]);
    }
}
