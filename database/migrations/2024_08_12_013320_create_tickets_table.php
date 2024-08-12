<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // Crea un campo auto-incremental 'id'
            $table->string('description'); // Campo 'description' de tipo string
            $table->string('ubication'); // Campo 'ubication' de tipo string
            $table->date('create_date'); // Campo 'create_date' de tipo date
            $table->string('status'); // Campo 'status' de tipo string
            $table->foreignId('user_id') // Campo 'user_id' como llave foránea
                  ->constrained() // Asume que la tabla relacionada es 'users' (por convención de Laravel)
                  ->onDelete('cascade'); // Si se elimina el usuario, se eliminan sus tickets
            $table->timestamps(); // Campos 'created_at' y 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
