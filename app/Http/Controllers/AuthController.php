<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mostrar la vista del formulario
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar los datos cuando el usuario haga clic en "Entrar"
    public function login(Request $request)
    {
        // Validamos que llenen los campos
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Intentamos iniciar sesión
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Si es su primer inicio, lo mandaremos a cambiar clave (lo haremos luego)
            // Por ahora, si entra bien, lo mandamos al panel principal
            return redirect()->intended('/dashboard');
        }

        // Si se equivoca en la clave o usuario:
        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('username');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}