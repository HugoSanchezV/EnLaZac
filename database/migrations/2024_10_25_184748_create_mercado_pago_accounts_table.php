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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercado_pago_accounts');
    }
};
