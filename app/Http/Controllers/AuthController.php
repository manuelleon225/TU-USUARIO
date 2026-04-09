<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Método para registrar un usuario
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ],
            // 👇 MENSAJES PERSONALIZADOS EN ESPAÑOL
            [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El correo es obligatorio',
                'email.email' => 'El correo no es válido',
                'email.unique' => 'Este correo ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener mínimo 6 caracteres'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Error en los datos enviados',
                'detalles' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

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
