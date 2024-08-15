<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id()->primary(); // Crea un campo auto-incremental 'id'
            $table->string('subject'); // Campo 'subject' de tipo string
            $table->string('description'); // Campo 'description' de tipo string
            $table->string('status')->default('0'); // Campo 'status' de tipo string
            $table->unsignedBigInteger('user_id') // Campo 'user_id' como llave foránea
                  ->constrained() // Asume que la tabla relacionada es 'users' (por convención de Laravel)
                  ->onDelete('cascade')
                  ->nullable()
                  ->default(null); // Si se elimina el usuario, se eliminan sus tickets
            $table->timestamps(); // Campos 'created_at' y 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
