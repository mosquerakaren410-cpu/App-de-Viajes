<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClimaServices
{
    public function obtenerClima(string $ciudad): array
    {
        try {

            $response = Http::timeout(5)->get(
                'https://api.openweathermap.org/data/2.5/weather',
                [
                    'q' => $ciudad,
                    'appid' => env('WEATHER_API_KEY'),
                    'units' => 'metric',
                    'lang' => 'es'
                ]
            );

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Clima no disponible'
                ];
            }

            $temperatura = $response->json('main.temp');

            if ($temperatura === null) {
                return [
                    'success' => false,
                    'message' => 'Temperatura no disponible'
                ];
            }

            return [
                'success' => true,
                'temperatura' => $temperatura
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'message' => 'No fue posible consultar el clima'
            ];
        }
    }
}