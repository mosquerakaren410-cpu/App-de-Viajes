<?php

namespace App\Services;

use App\Models\TasaCambio;
use Illuminate\Support\Facades\Http;


class MonedaServices
{
    public function obtenerTasa(
        string $destino
    )
    {
        try {

            $response =
                Http::timeout(5)
                    ->get(
                        "https://v6.exchangerate-api.com/v6/"
                        . env('EXCHANGE_API_KEY')
                        . "/latest/COP"
                    );

            if (
                !$response->successful()
            ) {

                return $this->buscarUltimaTasa(
                    $destino
                );
            }

            if (
                !isset(
                    $response['conversion_rates']
                )
            ) {

                return $this->buscarUltimaTasa(
                    $destino
                );
            }

            $tasa =
                $response
                    ['conversion_rates']
                    [$destino];

            TasaCambio::create([
                'origen' => 'COP',
                'destino' => $destino,
                'tasa' => $tasa,
                'fecha' => now()
            ]);

            return [
                'success' => true,
                'tasa' => $tasa,
                'source' => 'api'
            ];

        } catch (\Throwable $e) {

            return $this->buscarUltimaTasa(
                $destino
            );
        }
    }

    private function buscarUltimaTasa(
        string $destino
    )
    {
        $ultima =
            TasaCambio::where(
                'destino',
                $destino
            )
            ->latest()
            ->first();

        if ($ultima) {

            return [
                'success' => true,
                'tasa' => $ultima->tasa,
                'source' => 'cache'
            ];
        }

        return [
            'success' => false,
            'message' =>
                'Conversión no disponible'
        ];
    }
}