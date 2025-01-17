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
        Schema::create('payment_sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->contains('contracts')->onDelete('cascade');
            $table->boolean('status')->default(false);
            $table->boolean('applied')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_sanctions');
    }
};
