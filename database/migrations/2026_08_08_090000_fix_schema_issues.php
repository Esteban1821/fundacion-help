<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Correcciones al esquema detectadas en la revisión:
 *
 *  1. Se elimina 'adjunto_path'. La migración original creó esa columna,
 *     pero una migración posterior agregó 'soporte_adjunto' y el código
 *     terminó usando esta última. 'adjunto_path' quedó siempre vacía.
 *
 *  2. 'ratings.ticket_id' pasa a ser UNIQUE. El modelo declara hasOne,
 *     pero eso es solo una convención de Eloquent: la base de datos
 *     aceptaba varias calificaciones para el mismo ticket, lo que
 *     desviaba el promedio de satisfacción (CSAT).
 *
 *  3. Se agregan índices en 'estado' y 'created_at' de tickets, que son
 *     las columnas por las que filtra y agrupa el dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- 1. Columna sin uso ---
        if (Schema::hasColumn('tickets', 'adjunto_path')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('adjunto_path');
            });
        }

        // --- 2. Una sola calificación por ticket ---
        // Antes de crear el índice hay que limpiar posibles duplicados
        // ya existentes; si no, MySQL rechaza la restricción. Conservamos
        // la calificación más antigua de cada ticket.
        $duplicados = DB::table('ratings')
            ->select('ticket_id', DB::raw('MIN(id) as conservar'))
            ->groupBy('ticket_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicados as $fila) {
            DB::table('ratings')
                ->where('ticket_id', $fila->ticket_id)
                ->where('id', '!=', $fila->conservar)
                ->delete();
        }

        Schema::table('ratings', function (Blueprint $table) {
            $table->unique('ticket_id');
        });

        // --- 3. Índices para las consultas del dashboard ---
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('estado');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['created_at']);
            $table->string('adjunto_path')->nullable();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique(['ticket_id']);
        });
    }
};
