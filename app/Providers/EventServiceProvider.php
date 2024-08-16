<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TicketEvent;
use App\Listeners\TicketListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketEvent::class =>[
            TicketListener::class,
        ]
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
