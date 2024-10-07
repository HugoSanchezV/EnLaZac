<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function show()
    {
        //Varias consultas para mandar aca
        return Inertia::render('DashboardBase',[
            ''
        ]);
    }

    public function morrososCount(){
        
    }
}
