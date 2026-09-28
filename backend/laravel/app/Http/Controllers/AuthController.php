<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\TokenRevocado;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $existe = User::where('correo', $request->correo)->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'code' => 'USER_ALREADY_EXISTS',
            ], 409);
        }

        $usuario = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'password_hash' => Hash::make($request->password),
            'idioma' => 'es',
        ]);

        return response()->json([
            'success' => true,
            'data' => $usuario,
        ], 201);
    }

    public function login(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login:' . strtolower((string) $request->input('correo')) . '|' . $request->ip();
        $maxAttempts = 5;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_RATE_LIMITED',
                    'message' => 'Demasiados intentos de inicio de sesión. Intenta nuevamente más tarde.',
                    'retry_after' => RateLimiter::availableIn($key),
                ],
            ], 429);
        }

        $usuario = User::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'Correo o contraseña inválidos',
                ],
            ], 401);
        }

        RateLimiter::clear($key);

        $accessToken = $tokenService->generarAccessToken($usuario);
        $refreshToken = $tokenService->generarRefreshToken();

        RefreshToken::create([
            'user_id' => $usuario->id,
            'token' => $refreshToken,
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 900,
            ],
        ]);
    }

    public function refresh(Request $request, TokenService $tokenService)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $refreshToken = RefreshToken::where('token', $request->refresh_token)->first();

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_REFRESH_TOKEN',
                    'message' => 'El refresh token no es válido',
                ],
            ], 401);
        }

        // Un refresh token ya revocado fue reutilizado. Por seguridad,
        // se cierran todas las sesiones basadas en refresh del usuario.
        if ($refreshToken->revoked) {
            RefreshToken::where('user_id', $refreshToken->user_id)
                ->update(['revoked' => true]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_REFRESH_REUSE_DETECTED',
                    'message' => 'El refresh token ya fue utilizado. Las sesiones fueron cerradas.',
                ],
            ], 401);
        }

        if ($refreshToken->expires_at < now()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_REFRESH_TOKEN',
                    'message' => 'El refresh token no es válido',
                ],
            ], 401);
        }

        $usuario = User::find($refreshToken->user_id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_REFRESH_TOKEN',
                    'message' => 'El usuario no existe',
                ],
            ], 401);
        }

        // Rotación: el refresh usado queda inutilizable y se entrega uno nuevo.
        $refreshToken->update(['revoked' => true]);

        $nuevoRefreshToken = $tokenService->generarRefreshToken();
        RefreshToken::create([
            'user_id' => $usuario->id,
            'token' => $nuevoRefreshToken,
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        $accessToken = $tokenService->generarAccessToken($usuario);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $nuevoRefreshToken,
                'expires_in' => 900,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $payload = $request->attributes->get('token_payload');

        TokenRevocado::firstOrCreate([
            'jti' => $payload->jti,
        ], [
            'revoked_at' => now(),
        ]);

        RefreshToken::where('user_id', $payload->sub)
            ->update(['revoked' => true]);

        return response()->json([
            'success' => true,
        ]);
    }
}
