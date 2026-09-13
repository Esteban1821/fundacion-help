<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica que el control de acceso opere del lado del servidor y no
 * dependa de que la interfaz oculte los botones de navegación.
 *
 * Estas pruebas cubren el hallazgo detectado durante la fase de pruebas
 * de seguridad: el módulo de inventario ocultaba sus enlaces para el rol
 * Usuario, pero sus rutas seguían respondiendo a cualquier sesión
 * autenticada.
 */
class ControlDeAccesoPorRolTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $rol): User
    {
        return User::create([
            'name'      => 'Prueba',
            'last_name' => 'Automatizada',
            'username'  => 'prueba' . $rol . uniqid(),
            'cedula'    => (string) random_int(100000000, 999999999),
            'email'     => uniqid() . '@fundacionunivalle.com.co',
            'password'  => bcrypt('clave-de-prueba'),
            'role'      => $rol,
        ]);
    }

    public function test_un_usuario_comun_no_puede_entrar_al_inventario(): void
    {
        $usuario = $this->crearUsuario('usuario');

        $respuesta = $this->actingAs($usuario)->get('/inventory');

        $respuesta->assertForbidden();   // 403
    }

    public function test_un_usuario_comun_no_puede_entrar_a_gestion_de_usuarios(): void
    {
        $usuario = $this->crearUsuario('usuario');

        $respuesta = $this->actingAs($usuario)->get('/usuarios');

        $respuesta->assertForbidden();
    }

    public function test_un_usuario_comun_no_puede_entrar_al_panel_de_soporte(): void
    {
        $usuario = $this->crearUsuario('usuario');

        $respuesta = $this->actingAs($usuario)->get('/soporte/tickets');

        $respuesta->assertForbidden();
    }

    public function test_el_personal_de_soporte_si_puede_entrar_al_inventario(): void
    {
        $soporte = $this->crearUsuario('soporte');

        $respuesta = $this->actingAs($soporte)->get('/inventory');

        $respuesta->assertOk();
    }

    public function test_el_personal_de_soporte_no_puede_gestionar_usuarios(): void
    {
        $soporte = $this->crearUsuario('soporte');

        $respuesta = $this->actingAs($soporte)->get('/usuarios');

        $respuesta->assertForbidden();
    }

    public function test_el_administrador_puede_entrar_a_gestion_de_usuarios(): void
    {
        $admin = $this->crearUsuario('admin');

        $respuesta = $this->actingAs($admin)->get('/usuarios');

        $respuesta->assertOk();
    }

    public function test_una_visita_sin_sesion_es_enviada_al_inicio_de_sesion(): void
    {
        $respuesta = $this->get('/inventory');

        $respuesta->assertRedirect('/login');
    }
}
