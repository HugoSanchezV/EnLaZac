<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTicketOwnership;
use App\Http\Middleware\UpgradeToHttpsUnderNgrok;
use Illuminate\Support\Facades\Log as FacadesLog;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         api: __DIR__.'/../routes/api.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->web(append: [
//             \App\Http\Middleware\HandleInertiaRequests::class,
//             \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
//            // 'role'=>CheckRole::class,
//         ]);
//         $middleware->alias([
//             'rol' => CheckRole::class
//         ]);

//         //
//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })->create();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'telegram/webhook',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            UpgradeToHttpsUnderNgrok::class,  
            // Agrega aquí el middleware para ngrok
           // \App\Http\Middleware\LoadMailSettings::class,

           // 'role'=>CheckRole::class,
        ]);
        $middleware->alias([
            'rol' => CheckRole::class,
            'ticket' => CheckTicketOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        FacadesLog::info(json_encode($exceptions));
    })->create();
