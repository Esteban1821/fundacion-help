<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Mostrar lista de usuarios (Solo Admin)
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard')->withErrors(['Acceso denegado. Solo administradores.']);
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Mostrar formulario para crear usuario (Solo Admin)
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard')->withErrors(['Acceso denegado.']);
        }

        return view('users.create');
    }

    // Guardar el nuevo usuario
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        // 1. Limpieza y auto-generación del correo
        $usernameLimpio = strtolower(str_replace(' ', '', $request->username));
        $correoGenerado = $usernameLimpio . '@fundacionunivalle.com.co';

        $request->merge([
            'username' => $usernameLimpio,
            'email'    => $correoGenerado
        ]);

        // 2. Validación (Se agrega validación de cédula)
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:users,cedula',
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:admin,soporte,usuario',
        ]);

        // 3. Creación
        User::create([
            'cedula'     => $request->cedula, 
            'name'       => $request->name,
            'last_name'  => $request->last_name,
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'requires_password_change' => false
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    // Mostrar formulario para EDITAR usuario (NUEVO)
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);
        
        return view('users.edit', compact('user'));
    }

    // Guardar los CAMBIOS del usuario (NUEVO)
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        $usernameLimpio = strtolower(str_replace(' ', '', $request->username));
        $correoGenerado = $usernameLimpio . '@fundacionunivalle.com.co';

        $request->merge([
            'username' => $usernameLimpio,
            'email'    => $correoGenerado
        ]);

        // Validación con excepción de la cédula y usuario actual para no marcar duplicado
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:users,cedula,' . $user->id,
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,soporte,usuario',
            'password'  => 'nullable|string|min:8',
        ]);

        // Actualización de campos
        $user->cedula = $request->cedula; // <- Ahora también se actualiza la cédula
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }
}