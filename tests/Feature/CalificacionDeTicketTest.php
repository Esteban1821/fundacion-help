<?php

namespace Tests\Feature;

use App\Models\Rating;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica el control de acceso a nivel de objeto en la calificación del
 * servicio.
 *
 * Durante las pruebas de seguridad se detectó que la verificación de
 * propiedad del ticket se aplicaba únicamente al mostrar el formulario,
 * de modo que una petición enviada directamente al servidor permitía
 * calificar y cerrar el caso de otro usuario. Estas pruebas comprueban
 * que la validación se ejecuta también al guardar.
 */
class CalificacionDeTicketTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $rol = 'usuario'): User
    {
        return User::create([
            'name'      => 'Prueba',
            'last_name' => 'Automatizada',
            'username'  => 'u' . uniqid(),
            'cedula'    => (string) random_int(100000000, 999999999),
            'email'     => uniqid() . '@fundacionunivalle.com.co',
            'password'  => bcrypt('clave-de-prueba'),
            'role'      => $rol,
        ]);
    }

    private function crearTicket(User $dueno, string $estado = 'Atendido'): Ticket
    {
        return Ticket::create([
            'nro_ticket'                => 'TK-' . now()->format('Ymd') . '-' . random_int(100, 999),
            'user_id'                   => $dueno->id,
            'email_solicitante'         => $dueno->email,
            'area_solicitante'          => 'Calidad',
            'cargo_solicitante'         => 'Analista',
            'servicio_requerimiento'    => 'Hardware y Equipos',
            'subservicio_requerimiento' => 'El equipo no enciende',
            'descripcion_requerimiento' => 'Caso creado para pruebas automatizadas.',
            'prioridad'                 => 'Media',
            'estado'                    => $estado,
        ]);
    }

    public function test_el_dueno_puede_calificar_su_ticket_atendido(): void
    {
        $dueno  = $this->crearUsuario();
        $ticket = $this->crearTicket($dueno);

        $this->actingAs($dueno)
             ->post("/tickets/{$ticket->id}/calificar", [
                 'stars'    => 5,
                 'comments' => 'Atención oportuna.',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('ratings', ['ticket_id' => $ticket->id, 'stars' => 5]);
    }

    public function test_calificar_cierra_el_ticket_automaticamente(): void
    {
        $dueno  = $this->crearUsuario();
        $ticket = $this->crearTicket($dueno);

        $this->actingAs($dueno)
             ->post("/tickets/{$ticket->id}/calificar", ['stars' => 4]);

        $this->assertEquals('Cerrado', $ticket->fresh()->estado);
    }

    public function test_nadie_puede_calificar_un_ticket_ajeno(): void
    {
        $dueno    = $this->crearUsuario();
        $intruso  = $this->crearUsuario();
        $ticket   = $this->crearTicket($dueno);

        $this->actingAs($intruso)
             ->post("/tickets/{$ticket->id}/calificar", ['stars' => 1])
             ->assertForbidden();

        $this->assertDatabaseMissing('ratings', ['ticket_id' => $ticket->id]);
    }

    public function test_no_se_puede_calificar_un_ticket_que_sigue_abierto(): void
    {
        $dueno  = $this->crearUsuario();
        $ticket = $this->crearTicket($dueno, 'Abierto');

        $this->actingAs($dueno)
             ->post("/tickets/{$ticket->id}/calificar", ['stars' => 5])
             ->assertForbidden();
    }

    public function test_un_ticket_no_puede_calificarse_dos_veces(): void
    {
        $dueno  = $this->crearUsuario();
        $ticket = $this->crearTicket($dueno);

        Rating::create(['ticket_id' => $ticket->id, 'stars' => 3]);

        $this->actingAs($dueno)
             ->post("/tickets/{$ticket->id}/calificar", ['stars' => 5])
             ->assertForbidden();

        $this->assertEquals(1, Rating::where('ticket_id', $ticket->id)->count());
    }
}
