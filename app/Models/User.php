<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $fillable = [
    'cedula', // <- Asegúrate de que esté aquí
    'name',
    'last_name',
    'username',
    'email',
    'password',
    'role',
    'requires_password_change',
];

    protected $hidden = ['password'];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}