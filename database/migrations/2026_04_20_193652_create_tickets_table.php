<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nro_ticket')->unique(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email_solicitante');
            $table->string('cargo_solicitante');
            $table->string('area_solicitante');
            $table->string('servicio_requerimiento');
            $table->string('subservicio_requerimiento');
            $table->text('descripcion_requerimiento');
            $table->string('adjunto_path')->nullable(); 
            $table->enum('prioridad', ['Alta', 'Media', 'Baja']);
            $table->enum('estado', ['Abierto', 'Atendido', 'Cerrado', 'Otros'])->default('Abierto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};