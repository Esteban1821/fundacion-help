<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Estos son los campos que Laravel permitirá guardar
    protected $fillable = [
        'nro_ticket',
        'user_id',
        'email_solicitante',
        'area_solicitante',
        'cargo_solicitante',
        'servicio_requerimiento',
        'servicio_id',
        'subservicio_requerimiento',
        'subservicio_id',
        'descripcion_requerimiento',
        'prioridad',
        'soporte_adjunto',
        'estado',
        'atendido_por_id',
    ];

    // Relación: Un ticket pertenece a un Usuario (quien lo solicitó)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: técnico/agente que atendió el ticket
    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por_id');
    }

    // Relación: servicio del catálogo (puede ser null en tickets legados)
    public function servicio()
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }

    // Relación: subservicio del catálogo (puede ser null en tickets legados)
    public function subservicio()
    {
        return $this->belongsTo(Subservice::class, 'subservicio_id');
    }

    // 👇 AQUÍ ESTÁ LA MAGIA QUE FALTABA 👇
    // Relación: Un ticket tiene una (o ninguna) Calificación
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}