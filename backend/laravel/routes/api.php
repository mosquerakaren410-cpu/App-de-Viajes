<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaisController;
use App\Services\ClimaServices;
use App\Services\MonedaServices;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth.token')
    ->group(function () {

        Route::get('/test', function () {
            return response()->json([
                'ok' => true
            ]);
        });
    });

Route::middleware('auth.token')
        ->group(function () {

            Route::post('/auth/logout', [AuthController::class, 'logout']);

            Route::get('/paises', [PaisController::class, 'index']);
            Route::get('/paises/{id}/ciudades', [PaisController::class, 'ciudades']);


            
        });

Route::get('/test-clima', function (
    ClimaServices $service
) {
    return $service->obtenerClima('Tokyo');
});


Route::get('/test-moneda', function (
    MonedaServices $service
) {
    return $service->obtenerTasa('INR');
});