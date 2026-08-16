<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Aquí le decimos a Laravel qué campos se pueden guardar masivamente
    protected $fillable = [
        'category', 
        'description', 
        'serial_number', 
        'user_id'
    ];

    // Relación: Un equipo de inventario pertenece a un usuario (o a ninguno si está en bodega)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}