<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use App\Models\PaymentConfig;
use App\Models\PaypalAccount;
use Illuminate\Support\ServiceProvider;

class PayPalConfigServiceProvider extends ServiceProvider
{
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
        $paypalConfig = PaypalAccount::all()->first(); // Asume que tienes los datos de configuración de PayPal en la base de datos

        if ($paypalConfig) {
            // Actualizar los valores de la configuración de PayPal
            Config::set('paypal.sandbox.client_id', $paypalConfig->sandbox_client_id);
            Config::set('paypal.sandbox.client_secret', $paypalConfig->sandbox_client_secret);
            Config::set('paypal.live.client_id', $paypalConfig->live_client_id);
            Config::set('paypal.live.client_secret', $paypalConfig->live_client_secret);
            Config::set('paypal.mode', $paypalConfig->mode);
            Config::set('paypal.currency', $paypalConfig->currency);
        }
    }
}
