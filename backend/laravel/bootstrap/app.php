<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })

    ->withMiddleware(function ($middleware) {

        $middleware->alias([
            'auth.token' => App\Http\Middleware\AuthTokenMiddleware::class,
        ]);

        $middleware->append(\App\Http\Middleware\TraceIdMiddleware::class);

    })
    
    ->withExceptions(function ($exceptions) {

        $exceptions->render(
            function (
                ValidationException $e,
                $request

            ) {

                $details = [];

                foreach (
                    $e->errors() as $field => $messages
                ) {
            
                    $details[] = [
                        'field' => $field,
                        'message' => $messages[0]
                    ];

                }

                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'Hay campos con errores',
                        'details' => $details
                    ],

                    'trace_id' => $request->attributes->get('trace_id')
                ], 422);

            }
        );

        $exceptions->render(
            function (
                NotFoundHttpException $e,
                $request
            ) {

                return response()->json([

                    'success' => false,
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Recurso no encontrado'
                    ],

                    'trace_id' => $request->attributes->get('trace_id')

                ], 404);

            }
        );

        $exceptions->render(
            function (
                Throwable $e,
                $request
            ) {

                Log::error(
                    $e->getMessage(),
                    [
                        'trace_id' => $request->attributes->get('trace_id')
                    ]
                );

                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'INTERNAL_ERROR',
                        'message' => 'Error interno'
                    ],

                    'trace_id' => $request->attributes->get('trace_id')
                ], 500);

            }
        );

    })
    ->create();