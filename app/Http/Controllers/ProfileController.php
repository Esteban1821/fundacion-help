<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    // Mostrar formulario del perfil
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Guardar cambios del perfil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Opcional: puedes permitirles cambiar su username o email aquí si quieres
        ]);

        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Tu perfil ha sido actualizado.');
    }

    // Mostrar formulario para cambiar contraseña
    public function passwordEdit()
    {
        return view('profile.password');
    }

    // Guardar la nueva contraseña
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', // Valida que se sepa su clave actual
            'password'         => 'required|string|min:8|confirmed', // Debe coincidir con password_confirmation
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Tu contraseña ha sido cambiada de forma segura.');
    }
}