<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paypal_accounts', function (Blueprint $table) {
            $table->id(); 
            $table->string('mode')->default('sandbox'); // 'sandbox' o 'live'
            // $table->string('sandbox_client_id'); // Client ID para el entorno de pruebas
            // $table->string('sandbox_client_secret'); // Client Secret para el entorno de pruebas
            $table->string('live_client_id')->nullable(); // Client ID para el entorno de producción
            $table->string('live_client_secret')->nullable(); // Client Secret para el entorno de producción
            $table->string('currency')->default('USD'); // Moneda en la que se realizarán las transacciones
            $table->timestamps(); // Marca de tiempo para creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_accounts');
    }
};
