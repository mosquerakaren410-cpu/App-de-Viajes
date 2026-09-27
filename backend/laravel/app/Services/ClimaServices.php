<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClimaServices
{
    public function obtenerClima(string $ciudad)
    {
        try {

            $response = Http::timeout(5)->get(
                'https://api.openweathermap.org/data/2.5/weather',
                [
                    'q' => $ciudad,
                    'appid' => env('WEATHER_API_KEY'),
                    'units' => 'metric'
                ]
            );

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Clima no disponible'
                ];
            }

            if (
                !isset($response['main']) ||
                !isset($response['main']['temp'])
            ) {
                return [
                    'success' => false,
                    'message' => 'Clima no disponible'
                ];
            }

            return [
                'success' => true,
                'temperatura' => $response['main']['temp']
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}