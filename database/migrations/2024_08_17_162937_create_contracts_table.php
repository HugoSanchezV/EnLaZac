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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id(); // Id autoincremental
            $table->unsignedBigInteger('user_id'); // Llave foránea a users
            $table->unsignedBigInteger('plan_id'); // Llave foránea a plans
            $table->string('address'); // Dirección del contrato
            $table->json('geolocation'); // Ubicacion de tipo JSON para poner la Latitud y la Longitud
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
