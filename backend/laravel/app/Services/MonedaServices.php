<?php

namespace App\Services;

use App\Models\TasaCambio;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MonedaServices
{
    public function obtenerTasa(string $destino): array
    {
        try {

            $response = Http::timeout(5)
                ->get(
                    "https://v6.exchangerate-api.com/v6/"
                    . env('EXCHANGE_API_KEY')
                    . "/latest/COP"
                );

            if (!$response->successful()) {

                return $this->buscarUltimaTasa($destino);
            }

            if (!$response->has('conversion_rates')) {

                return $this->buscarUltimaTasa($destino);
            }

            $tasa = $response->json(
                "conversion_rates.$destino"
            );

            if ($tasa === null) {

                return $this->buscarUltimaTasa($destino);
            }

            /*
             * ExchangeRate-API proporciona la fecha
             * de actualización de las tasas.
             */
            $fecha = null;

            if ($response->has('time_last_update_utc')) {

                $fecha = Carbon::parse(
                    $response->json('time_last_update_utc')
                )->format('Y-m-d');
            }

            /*
             * Guardamos la tasa obtenida de la API
             * para utilizarla como caché si la API
             * falla posteriormente.
             */
            TasaCambio::create([
                'origen' => 'COP',
                'destino' => $destino,
                'tasa' => $tasa,
                'fecha' => $fecha ?? now()->toDateString()
            ]);

            return [
                'success' => true,
                'tasa' => $tasa,
                'fecha' => $fecha ?? now()->toDateString(),
                'source' => 'api'
            ];

        } catch (\Throwable $e) {

            return $this->buscarUltimaTasa($destino);
        }
    }

    private function buscarUltimaTasa(string $destino): array
    {
        $ultima = TasaCambio::where(
            'destino',
            $destino
        )
        ->latest('fecha')
        ->first();

        if ($ultima) {

            return [
                'success' => true,
                'tasa' => $ultima->tasa,
                'fecha' => $ultima->fecha,
                'source' => 'cache'
            ];
        }

        return [
            'success' => false,
            'message' => 'Conversión no disponible'
        ];
    }
}