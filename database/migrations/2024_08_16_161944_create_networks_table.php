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
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            // router de la tabla router
            $table->unsignedBigInteger('router_id'); 
            
            $table->string("address");
            $table->ipAddress("network");
            $table->timestamps();

            $table->foreign('router_id')->references('id')->on('routers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
