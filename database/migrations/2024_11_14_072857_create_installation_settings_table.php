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
        Schema::create('installation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->nullable()->constrained('installations')->onDelete('cascade'); // Relación con cliente
            $table->integer('exemption_months')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_settings');
    }
};
