<?php

namespace App\Providers;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Inertia::share([
            'auth.user' => function () {
                return Auth::user() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'admin' => Auth::user()->admin,
                ] : null;
            },
        ]);

        // $mail = MailSetting::first();
        // $data = [
        //     'driver' => $mail->transport,
        //     'host' => $mail->host,
        //     'port' => $mail->port,
        //     'encryption' =>  $mail->encryption,
        //     'username' => $mail->username,
        //     'password' => $mail->password,
        //     'from' => [
        //         'address' => $mail->address,
        //         'name' => $mail->name
        //     ]
        // ];
        // Config::set('mail', $data);
    }
}
