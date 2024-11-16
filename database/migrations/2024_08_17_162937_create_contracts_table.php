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
            $table->unsignedBigInteger('inv_device_id')->nullable()->constrained('inventorie_devices')->onDelete('set null'); // Llave foránea a users
            $table->unsignedBigInteger('plan_id')->nullable()->constrained('plans')->onDelete('set null'); // Llave foránea a plans
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active');
            $table->string('address'); // Dirección del contrato
            $table->foreignId('rural_community_id')->nullable()->constrained('rural_communities');
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
