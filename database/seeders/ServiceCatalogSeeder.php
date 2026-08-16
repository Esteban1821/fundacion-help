<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Subservice;
use Illuminate\Database\Seeder;

class ServiceCatalogSeeder extends Seeder
{
    /**
     * Mismo diccionario que antes vivía hardcodeado en
     * TicketController@create, ahora como catálogo real en BD.
     * Agregar/editar servicios ya no requiere tocar código PHP.
     */
    protected array $catalogo = [
        'Usuarios y Accesos' => [
            'Bloqueo de Usuario / Directorio Activo',
            'Creación de Usuario Nuevo',
            'Reinicio de Contraseña',
            'Permisos en Carpetas Compartidas',
        ],
        'Hardware y Equipos' => [
            'El equipo no enciende',
            'Falla en Teclado / Mouse',
            'Impresora sin tóner / atascada',
            'Asignación de Equipo Nuevo',
        ],
        'Software y Aplicaciones' => [
            'Instalación de Office / Programas',
            'Error en Sistema SIESA',
            'Configuración de Correo Electrónico',
        ],
        'Redes y Conectividad' => [
            'Falla de conexión a Internet',
            'Punto de red inactivo',
            'Configuración de VPN',
        ],
    ];

    public function run(): void
    {
        $ordenServicio = 0;

        foreach ($this->catalogo as $nombreServicio => $subservicios) {
            $servicio = Service::updateOrCreate(
                ['name' => $nombreServicio],
                ['orden' => $ordenServicio++]
            );

            $ordenSub = 0;
            foreach ($subservicios as $nombreSub) {
                Subservice::updateOrCreate(
                    ['service_id' => $servicio->id, 'name' => $nombreSub],
                    ['orden' => $ordenSub++]
                );
            }
        }

        $this->vincularTicketsExistentes();
    }

    /**
     * Los tickets creados ANTES de este catálogo solo tienen el nombre
     * del servicio/subservicio como texto libre (servicio_requerimiento).
     * Aquí los enlazamos al catálogo por coincidencia de nombre para que
     * el dashboard los agrupe correctamente sin perder el histórico.
     * Los que no encuentren coincidencia exacta simplemente quedan sin
     * enlazar (servicio_id null) y el dashboard los reporta como "Otros".
     */
    protected function vincularTicketsExistentes(): void
    {
        $servicios = Service::all()->keyBy('name');

        \App\Models\Ticket::whereNull('servicio_id')->orWhereNull('subservicio_id')
            ->get()
            ->each(function ($ticket) use ($servicios) {
                $servicio = $servicios->get($ticket->servicio_requerimiento);
                if (!$servicio) {
                    return;
                }

                $subservicio = $servicio->subservicios->firstWhere('name', $ticket->subservicio_requerimiento);

                $ticket->servicio_id = $servicio->id;
                $ticket->subservicio_id = $subservicio?->id;
                $ticket->saveQuietly();
            });
    }
}
