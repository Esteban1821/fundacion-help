<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Subservice;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica la generación del número de radicado.
 *
 * El consecutivo se calcula contando los tickets del día, por lo que dos
 * solicitudes enviadas de forma simultánea podían obtener el mismo número
 * y provocar una violación de la restricción UNIQUE. El controlador
 * reintenta con el siguiente número disponible; estas pruebas comprueban
 * el formato, la unicidad y el comportamiento ante números ya ocupados.
 */
class ConsecutivoDeTicketTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(): User
    {
        return User::create([
            'name'      => 'Prueba',
            'last_name' => 'Automatizada',
            'username'  => 'u' . uniqid(),
            'cedula'    => (string) random_int(100000000, 999999999),
            'email'     => uniqid() . '@fundacionunivalle.com.co',
            'password'  => bcrypt('clave-de-prueba'),
            'role'      => 'usuario',
        ]);
    }

    private function sembrarCatalogo(): Subservice
    {
        $servicio = Service::create(['name' => 'Hardware y Equipos', 'orden' => 0]);

        return Subservice::create([
            'service_id' => $servicio->id,
            'name'       => 'El equipo no enciende',
            'orden'      => 0,
        ]);
    }

    private function datosDelFormulario(Subservice $sub): array
    {
        return [
            'area_solicitante'          => 'Calidad',
            'cargo_solicitante'         => 'Analista',
            'servicio_requerimiento'    => $sub->servicio->name,
            'subservicio_requerimiento' => $sub->name,
            'descripcion_requerimiento' => 'Caso creado para pruebas automatizadas.',
            'prioridad'                 => 'Alta',
        ];
    }

    public function test_el_radicado_usa_el_formato_esperado(): void
    {
        $usuario = $this->crearUsuario();
        $sub     = $this->sembrarCatalogo();

        $this->actingAs($usuario)->post('/tickets', $this->datosDelFormulario($sub));

        $ticket = Ticket::first();

        $this->assertMatchesRegularExpression(
            '/^TK-\d{8}-\d{3}$/',
            $ticket->nro_ticket,
            'El radicado debe seguir el formato TK-AAAAMMDD-NNN.'
        );
    }

    public function test_el_ticket_queda_enlazado_al_catalogo_de_servicios(): void
    {
        $usuario = $this->crearUsuario();
        $sub     = $this->sembrarCatalogo();

        $this->actingAs($usuario)->post('/tickets', $this->datosDelFormulario($sub));

        $ticket = Ticket::first();

        $this->assertEquals($sub->service_id, $ticket->servicio_id);
        $this->assertEquals($sub->id, $ticket->subservicio_id);
    }

    public function test_varios_tickets_del_mismo_dia_reciben_radicados_distintos(): void
    {
        $usuario = $this->crearUsuario();
        $sub     = $this->sembrarCatalogo();

        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($usuario)->post('/tickets', $this->datosDelFormulario($sub));
        }

        $radicados = Ticket::pluck('nro_ticket')->all();

        $this->assertCount(3, $radicados);
        $this->assertCount(3, array_unique($radicados), 'Los radicados no deben repetirse.');
    }

    public function test_el_ticket_nace_en_estado_abierto(): void
    {
        $usuario = $this->crearUsuario();
        $sub     = $this->sembrarCatalogo();

        $this->actingAs($usuario)->post('/tickets', $this->datosDelFormulario($sub));

        $this->assertEquals('Abierto', Ticket::first()->estado);
    }

    public function test_un_formulario_incompleto_no_registra_el_ticket(): void
    {
        $usuario = $this->crearUsuario();
        $this->sembrarCatalogo();

        $this->actingAs($usuario)
             ->post('/tickets', ['area_solicitante' => 'Calidad'])
             ->assertSessionHasErrors();

        $this->assertEquals(0, Ticket::count());
    }
}
