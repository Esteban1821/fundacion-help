<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'orden'];

    public function subservicios(): HasMany
    {
        return $this->hasMany(Subservice::class)->orderBy('orden')->orderBy('name');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'servicio_id');
    }
}
