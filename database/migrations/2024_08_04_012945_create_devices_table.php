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
            $table->string('device_internal_id');

            $table->unsignedBigInteger('router_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string("comment")->nullable()->default(null);
            $table->string("list");
            $table->ipAddress("address")->uniqid();
            $table->time("creation_time");
            $table->boolean("disabled")->default(true);

            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('router_id')->references('id')->on('routers')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('inventorie_devices')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['router_id']);
            $table->dropForeign(['device_id']);
            $table->dropForeign(['user_id']);
        });


        //Schema::dropIfExists('routers');
        Schema::dropIfExists('devices');
    }
};
