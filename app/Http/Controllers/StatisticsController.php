<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function show()
    {
        //Varias consultas para mandar aca
        $morrosos = self::morrososCount();
        $active = self::activeDevices();
        $newTickets = self::currentTickets();
        return Inertia::render('DashboardBase',[
            'morrosos' => $morrosos,
            'active' => $active,
            'new_tickets' =>$newTickets,


        ]);
    }

    public function morrososCount(){
        $device = (Device::where('list','MOROSOS'))->count();
        return $device;
    }

    public function activeDevices()
    {
        $device = (Device::where('disabled','0'))->count();
        return $device;
    }

    public function currentTickets()
    {
        $currentDate = Carbon::now()->format('Y-m-d');  // Obtener solo la fecha actual
        $tickets = Ticket::whereDate('created_at', $currentDate)->count();

        return $tickets;

    }
}
