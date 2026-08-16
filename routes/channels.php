<?php

use Illuminate\Support\Facades\Broadcast;

// Canal privado del dashboard: cualquier usuario autenticado puede
// escuchar las actualizaciones (los datos que viajan por el canal ya
// están filtrados por rol en DashboardController, así que no se expone
// nada que el usuario no pudiera ver entrando al dashboard normal).
Broadcast::channel('dashboard', function ($user) {
    return $user !== null;
});
