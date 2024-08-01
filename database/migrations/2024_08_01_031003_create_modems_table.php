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
        Schema::create('modems', function (Blueprint $table) {
            $table->id();
            $table->integer("router_id");
            $table->ipAddress("network");
            $table->ipAddress("interface");
            $table->string("current_interface");
            $table->boolean("disabled");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modems');
    }
};
