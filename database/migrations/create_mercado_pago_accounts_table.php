<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadoPagoAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('mercado_pago_accounts', function (Blueprint $table) {
            $table->id(); // ID autoincremental.
            $table->enum('mode', ['sandbox', 'live']); // Modo: sandbox o live.
            $table->string('sandbox_public_key'); // Clave pública para sandbox.
            $table->string('sandbox_access_token'); // Token de acceso para sandbox.
            $table->string('live_public_key')->nullable(); // Clave pública para live.
            $table->string('live_access_token')->nullable(); // Token de acceso para live.
            $table->string('currency', 3); // Moneda en formato ISO (ej: USD).
            $table->timestamps(); // Marca de tiempo de creación y actualización.
        });
    }

    public function down()
    {
        Schema::dropIfExists('mercado_pago_accounts'); // Elimina la tabla si se revierte la migración.
    }
}