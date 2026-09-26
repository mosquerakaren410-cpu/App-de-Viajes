<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TokenService;
use App\Models\User;
use App\Models\TokenRevocado;
use Firebase\JWT\ExpiredException;
use Throwable;

class AuthTokenMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response
    {
        $header = $request->header('Authorization');

        if (
            !$header ||
            !str_starts_with($header, 'Bearer ')
        ) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_TOKEN_MISSING'
                ]
            ], 401);
        }

        try {

            $token = str_replace(
                'Bearer ',
                '',
                $header
            );

            $tokenService =
                app(TokenService::class);

            $payload =
                $tokenService
                    ->validarAccessToken($token);

            if (
                TokenRevocado::where(
                    'jti',
                    $payload->jti
                )->exists()
            ) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'AUTH_TOKEN_REVOKED'
                    ]
                ],401);
            }

            $usuario = User::find(
                $payload->sub
            );

            if (!$usuario) {

                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'AUTH_TOKEN_INVALID'
                    ]
                ],401);
            }

            $request->attributes->set(
                'usuario',
                $usuario
            );

            return $next($request);

        } catch (ExpiredException $e) {

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_TOKEN_EXPIRED'
                ]
            ],401);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_TOKEN_INVALID'
                ]
            ],401);
        }
    }
}