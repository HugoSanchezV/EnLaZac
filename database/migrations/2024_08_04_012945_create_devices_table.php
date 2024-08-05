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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->integer("router_id")->references('id')->on('routers')->onDelete('cascade');
            $table->ipAddress("network");
            $table->ipAddress("interface");
            $table->string("current_interface");
            $table->integer("device")->references('id')->on('inventoriedevices')->default(null)->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
