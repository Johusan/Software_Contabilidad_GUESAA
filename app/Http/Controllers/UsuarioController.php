<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    /**
     * Display a listing of system users (Administrators only).
     */
    public function index(Request $request)
    {
        if ($request->user()->id_rol !== 1) {
            return redirect('/dashboard')->with('error', 'Acceso denegado. Módulo exclusivo para Administradores.');
        }

        $usuarios = Usuario::with('rol')->orderBy('id_usuario', 'asc')->get();
        $roles = Rol::orderBy('id_rol', 'asc')->get();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a new user with assigned role.
     */
    public function store(Request $request)
    {
        if ($request->user()->id_rol !== 1) {
            return back()->withErrors(['error' => 'Solo los Administradores pueden registrar nuevos usuarios.']);
        }

        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ]);

        Usuario::create([
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'id_rol' => $request->input('id_rol'),
            'estado' => true,
        ]);

        return redirect()->back()->with('success', 'Usuario registrado con éxito.');
    }

    /**
     * Update an existing user and their role.
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->id_rol !== 1) {
            return back()->withErrors(['error' => 'Solo los Administradores pueden actualizar usuarios.']);
        }

        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'email')->ignore($id, 'id_usuario'),
            ],
            'id_rol' => 'required|integer|exists:roles,id_rol',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'nombres' => $request->input('nombres'),
            'apellidos' => $request->input('apellidos'),
            'email' => strtolower($request->input('email')),
            'id_rol' => $request->input('id_rol'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $usuario->update($data);

        return redirect()->back()->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Toggle active status of a user.
     */
    public function toggleEstado(Request $request, $id)
    {
        if ($request->user()->id_rol !== 1) {
            return back()->withErrors(['error' => 'Solo los Administradores pueden cambiar el estado de un usuario.']);
        }

        if (intval($id) === intval(Auth::user()->id_usuario)) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta de Administrador.']);
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->estado = !$usuario->estado;
        $usuario->save();

        $estadoTexto = $usuario->estado ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario {$estadoTexto} con éxito.");
    }
}
