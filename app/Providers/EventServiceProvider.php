<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TicketEvent;
use App\Events\RegisterUserEvent;
use App\Listeners\ContractWarningListener;
use App\Events\ContractWarningEvent;
use App\Listeners\ContractWarningListen;
use App\Listeners\RegisterUserListener;
use App\Listeners\TicketListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketEvent::class =>[
            TicketListener::class,
        ],
        RegisterUserEvent::class =>[
            RegisterUserListener::class,
        ],
        ContractWarningEvent::class => [
            ContractWarningListener::class,
        ],
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
