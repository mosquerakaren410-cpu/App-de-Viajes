<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\TokenService;
use App\Models\RefreshToken;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
        'nombre' => 'required|string|max:255',
        'correo' => 'required|email',
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->numbers()
        ]
    ]);

    $existe = User::where('correo', $request->correo)->first();

    if ($existe) {
        return response()->json([
            'success' => false,
            'code' => 'USER_ALREADY_EXISTS'
        ], 409);
    }

    $usuario = User::create([
        'nombre' => $request->nombre,
        'correo' => $request->correo,
        'password_hash' => Hash::make($request->password),
        'idioma' => 'es'
    ]);

    return response()->json([
        'success' => true,
        'data' => $usuario
    ], 201);
    }

    public function login(Request $request, TokenService $tokenService) {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = User::where(
            'correo',
            $request->correo
        )->first();

        if (!$usuario || !Hash::check(

            $request->password,
            $usuario->password_hash
        )
        
    ) {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Correo o contraseña inválidos'
            ]
        ], 401);
    }

    $accessToken = $tokenService->generarAccessToken($usuario);

    $refreshToken = $tokenService->generarRefreshToken();

    RefreshToken::create([
        'user_id' => $usuario->id,
        'token' => $refreshToken,
        'expires_at' => now()->addDays(30)
    ]);

    return response()->json([
        'success' => true,
        'data' => [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 900
        ]
    ]);

    }

}
