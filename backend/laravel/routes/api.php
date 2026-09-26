<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

        });