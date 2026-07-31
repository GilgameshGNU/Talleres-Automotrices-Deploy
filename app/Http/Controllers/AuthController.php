<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'     => 'required|string',
            'contrasena' => 'required|string',
        ]);

        $loginInput = trim($credentials['login']);
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nombreUsuario';

        $authAttempt = Auth::attempt([
            $field    => $loginInput,
            'password' => $credentials['contrasena']
        ]);

        if ($authAttempt) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Sesión iniciada con éxito',
                'usuario' => Auth::user()->load('rol')
            ]);
        }

        return response()->json([
            'message' => 'Las credenciales proporcionadas son incorrectas.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión cerrada con éxito'
        ]);
    }

    /**
     * Get the authenticated user with their role.
     */
    public function me()
    {
        if (Auth::check()) {
            return response()->json(Auth::user()->load('rol'));
        }

        return response()->json([
            'message' => 'No autenticado'
        ], 401);
    }
}
