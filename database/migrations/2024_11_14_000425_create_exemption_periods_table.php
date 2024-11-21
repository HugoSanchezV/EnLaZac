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
        Schema::create('exemption_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('start_day'); // Fecha de inicio de exención
            $table->integer('end_day');   // Fecha de fin de exención
            $table->integer('month_next');
            $table->integer('month_after_next');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exemption_periods');
    }
};
