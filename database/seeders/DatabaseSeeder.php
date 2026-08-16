<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Admin - puede gestionar usuarios
        User::create([
            'name' => 'David',
            'last_name' => 'Giron',
            'username' => 'dgiron',
            'cedula' => '123456789',
            'email' => 'admin@fundacion.edu.co',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'requires_password_change' => false,
        ]);

        // Usuario Soporte - atiende tickets
        User::create([
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'username' => 'jperez',
            'cedula' => '987654321',
            'email' => 'soporte@fundacion.edu.co',
            'password' => Hash::make('123456789'),
            'role' => 'soporte',
            'requires_password_change' => false,
        ]);

        // Usuario Normal - crea tickets
        User::create([
            'name' => 'Carlos',
            'last_name' => 'Martínez',
            'username' => 'cmartinez',
            'cedula' => '555666777',
            'email' => 'usuario@fundacion.edu.co',
            'password' => Hash::make('123456789'),
            'role' => 'usuario',
            'requires_password_change' => false,
        ]);

        // Catálogo real de servicios/subservicios (reemplaza el
        // diccionario que antes estaba hardcodeado en el controlador)
        $this->call(ServiceCatalogSeeder::class);

        // Ejecutar el seeder de inventarios
        $this->call(InventorySeeder::class);
    }
}