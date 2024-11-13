<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use Closure;
//use DragonCode\Contracts\Cashier\Auth\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckTicketOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check())
        {
            $ticketExists = Ticket::where('user_id', Auth::user()->id)
            ->where('id', $request->id)
            ->exists();    
            
            if($ticketExists){
                return $next($request);
            }
            
        }
        return redirect('/tickets/usuario')->with('error', 'Ticket no valido.');

    }
}
