<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Método para registrar un usuario
     */
    public function register(Request $request)
    {
        // Validar los datos que llegan desde el cliente
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        // Crear el usuario en la base de datos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Encriptar la contraseña por seguridad
            'password' => Hash::make($request->password)
        ]);

        // Retornar respuesta exitosa
        return response()->json([
            'mensaje' => 'Usuario registrado correctamente'
        ], 201);
    }

    /**
     * Método para iniciar sesión
     */
    public function login(Request $request)
    {
        // Validar datos recibidos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña coincide
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Error en la autenticación'
            ], 401);
        }

        // Si todo es correcto
        return response()->json([
            'mensaje' => 'Autenticación satisfactoria'
        ], 200);
    }
}