<?php

namespace App\Http\Controllers;

use App\Http\Requests\Interest\UpdateInterestRequest;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use PDO;

class InterestsController extends Controller
{
    public function edit()
    {
        $interest1 = Interest::where('name', 'fuera-fecha')->first();
        $interest2 = Interest::where('name', 'recargo-mes')->first();

        return Inertia::render('Admin/Settings/Interest/Edit', [
            'interestCourt' => $interest1,
            'interestDebt' => $interest2,

        ]);
    }
    public function update(UpdateInterestRequest $request)
    {

        $validatedData = $request->validated();

        $interest = Interest::where('name','fuera-fecha')->first();
        if(!$interest)
        {
            Interest::create([
                'name' => "fuera-fecha",
                'amount' => $validatedData['amountCourt'],

            ]);
        }else{
            
            $interest->update([
                'amount' => $validatedData['amountCourt'],
            ]);
        }

        $interest = Interest::where('name','recargo-mes')->first();
        if(!$interest)
        {
            Interest::create([
                'name' => "recargo-mes",
                'amount' => $validatedData['amountDebt'],

            ]);
        }else{
            
            $interest->update([
                'amount' => $validatedData['amountDebt'],
            ]);
        }

        return redirect()->route('settings.interest')->with('success', 'El interest ha sido Actualizado Con Éxito');
    }
    
    public function createInterestCourtDate()
    {
        Interest::create([
            'name' => "fuera-fecha",
            'amount' => 50
        ]);
    }

    public function createInterestMounthsDebt()
    {
        Interest::create([
            'name' => "recargo-mes",
            'amount' => 50
        ]);
    }

    public function getInterest($name){
        return Interest::where('name',$name)->first();
    }

}
