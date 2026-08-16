<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$users = User::all();
echo "\n=== USUARIOS CREADOS ===\n";
foreach($users as $user) {
    echo "- {$user->name} ({$user->username}) | Rol: {$user->role} | Email: {$user->email}\n";
}
echo "\nTotal de usuarios: " . count($users) . "\n";

$inventarios = \App\Models\Inventory::count();
echo "Total de inventarios: " . $inventarios . "\n\n";
