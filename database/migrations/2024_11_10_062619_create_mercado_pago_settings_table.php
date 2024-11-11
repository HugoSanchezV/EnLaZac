<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadoPagoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mercado_pago_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['sandbox', 'live'])->default('sandbox');
            $table->string('sandbox_client_id');
            $table->string('sandbox_client_secret');
            $table->string('live_client_id')->nullable();
            $table->string('live_client_secret')->nullable();
            $table->string('currency')->default('MXN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mercado_pago_settings');
    }
}
