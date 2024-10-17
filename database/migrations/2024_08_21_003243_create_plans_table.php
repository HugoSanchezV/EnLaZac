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
        Schema::create('plans', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->string('description'); // Campo normal descripción
            $table->decimal('price');
            $table->json('burst_limit'); // Campo JSON para los límites de subida
            $table->json('burst_threshold'); // Campo JSON para los límites de bajada
            $table->json('burst_time'); // Campo JSON para los límites de bajada
            $table->json('limite_at'); // Campo JSON para los límites de bajada
            $table->json('max_limit'); // Campo JSON para los límites de bajada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
