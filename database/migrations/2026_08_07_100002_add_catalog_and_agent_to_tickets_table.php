<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Referencias al catálogo. Nullable a propósito: los tickets
            // históricos ya tienen 'servicio_requerimiento'/'subservicio_requerimiento'
            // como texto y siguen mostrándose igual; los tickets nuevos
            // además guardan estas referencias para que el dashboard
            // agrupe por catálogo real y no por coincidencia exacta de texto.
            $table->foreignId('servicio_id')->nullable()->after('servicio_requerimiento')
                ->constrained('services')->nullOnDelete();

            $table->foreignId('subservicio_id')->nullable()->after('subservicio_requerimiento')
                ->constrained('subservices')->nullOnDelete();

            // Técnico/agente que atendió el ticket (para el reporte de
            // tiempo promedio de resolución por técnico). Se asigna
            // automáticamente al primer soporte/admin que lo actualiza.
            $table->foreignId('atendido_por_id')->nullable()->after('estado')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('servicio_id');
            $table->dropConstrainedForeignId('subservicio_id');
            $table->dropConstrainedForeignId('atendido_por_id');
        });
    }
};
