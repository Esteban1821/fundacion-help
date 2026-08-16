<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subservice extends Model
{
    protected $fillable = ['service_id', 'name', 'orden'];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'subservicio_id');
    }
}
