<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('category', [
                'Asignación Equipos Computo', 
                'Asignacion Monitores', 
                'Asignacion Teclado', 
                'Asignacion Mouse', 
                'Asignacion Modem', 
                'Asignacion Plan Celular', 
                'Otros Dispositivos'
            ]);
            $table->string('description');
            $table->string('serial_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};