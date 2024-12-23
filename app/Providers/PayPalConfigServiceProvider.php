<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use App\Models\PaymentConfig;
use App\Models\PaypalAccount;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable('paypal_accounts')) {
            $paypalConfig = PaypalAccount::first(); // Obtén el primer registro

            if ($paypalConfig) {
                // Actualizar los valores de la configuración de PayPal
                Config::set('paypal.sandbox.client_id', $paypalConfig->sandbox_client_id);
                Config::set('paypal.sandbox.client_secret', $paypalConfig->sandbox_client_secret);
                Config::set('paypal.live.client_id', $paypalConfig->live_client_id);
                Config::set('paypal.live.client_secret', $paypalConfig->live_client_secret);
                Config::set('paypal.mode', $paypalConfig->mode);
                Config::set('paypal.currency', $paypalConfig->currency);
            }
        } else {
            logger('La tabla paypal_accounts no existe aún.');
        }
    }
}
