<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\ConsultaController;
use App\Services\ClimaServices;
use App\Services\MonedaServices;
use App\Services\TokenService;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth.token')
    ->group(function () {

        Route::get('/test', function (Request $request) {
            $usuario = $request->attributes->get('usuario');

            return response()->json([
                'ok' => true,
                'user_id' => $usuario?->id,
                'correo' => $usuario?->correo,
            ]);
        });
    });

Route::middleware('auth.token')
        ->group(function () {

            Route::post('/auth/logout', [AuthController::class, 'logout']);

            Route::get('/paises', [PaisController::class, 'index']);
            Route::get('/paises/{id}/ciudades', [PaisController::class, 'ciudades']);

            Route::post('/consultas', [ConsultaController::class, 'store']);

            Route::get('/consultas/historial', [ConsultaController::class, 'historial']);
           
        });

if (app()->environment(['local', 'testing'])) {
    Route::middleware('auth.token')->get('/test/expired-token', function (Request $request, TokenService $tokenService) {
        $usuario = $request->attributes->get('usuario');

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $tokenService->generarAccessToken($usuario, -1),
            ],
        ]);
    });
}

Route::get('/test-clima', function (ClimaServices $service) {
    return $service->obtenerClima('Tokyo');
});


Route::get('/test-moneda', function (MonedaServices $service) {
    return $service->obtenerTasa('INR');
});