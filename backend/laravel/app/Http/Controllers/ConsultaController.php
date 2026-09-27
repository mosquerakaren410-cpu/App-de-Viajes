<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClimaServices;
use App\Services\MonedaServices;
use App\Models\Ciudad;
use App\Models\Consulta;

class ConsultaController extends Controller
{
    /**
     * POST /api/consultas
     */
    public function store(
        Request $request,
        ClimaServices $climaService,
        MonedaServices $monedaService
    ) {

        /*
        |--------------------------------------------------------------------------
        | 1. Validar información recibida
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([
            'ciudad_id' => [
                'required',
                'integer',
                'exists:ciudades,id'
            ],

            'presupuesto' => [
                'required',
                'numeric',
                'gt:0'
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Obtener usuario del token
        |--------------------------------------------------------------------------
        */

        $usuario = $request->attributes->get('usuario');

        if (!$usuario) {

            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Obtener ciudad, país y moneda
        |--------------------------------------------------------------------------
        */

        $ciudad = Ciudad::with('pais.moneda')
            ->find($data['ciudad_id']);

        if (!$ciudad) {

            return response()->json([
                'success' => false,
                'message' => 'La ciudad no existe'
            ], 422);
        }

        if (!$ciudad->pais) {

            return response()->json([
                'success' => false,
                'message' => 'La ciudad no tiene un país asociado'
            ], 422);
        }

        if (!$ciudad->pais->moneda) {

            return response()->json([
                'success' => false,
                'message' => 'El país no tiene una moneda asociada'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Consultar clima
        |--------------------------------------------------------------------------
        */

        $clima = $climaService->obtenerClima(
            $ciudad->nombre
        );

        if (!$clima['success']) {

            return response()->json([
                'success' => false,
                'message' => 'No fue posible obtener el clima de la ciudad'
            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Obtener moneda
        |--------------------------------------------------------------------------
        */

        $moneda = $ciudad->pais->moneda;


        /*
        |--------------------------------------------------------------------------
        | 6. Consultar tasa de cambio
        |--------------------------------------------------------------------------
        */

        $tasa = $monedaService->obtenerTasa(
            $moneda->codigo
        );

        if (!$tasa['success']) {

            return response()->json([
                'success' => false,
                'message' => 'No fue posible obtener la tasa de cambio'
            ], 502);
        }


        /*
        |--------------------------------------------------------------------------
        | 7. Calcular conversión
        |--------------------------------------------------------------------------
        */

        $valorConvertido =
            $data['presupuesto'] * $tasa['tasa'];


        /*
        |--------------------------------------------------------------------------
        | 8. Guardar consulta completa
        |--------------------------------------------------------------------------
        */

        $consulta = Consulta::create([

            'user_id' =>
                $usuario->id,

            'ciudad_id' =>
                $ciudad->id,

            'pais' =>
                $ciudad->pais->nombre,

            'ciudad' =>
                $ciudad->nombre,

            'presupuesto_cop' =>
                $data['presupuesto'],

            'clima_c' =>
                $clima['temperatura'],

            'moneda' =>
                $moneda->nombre,

            'simbolo_moneda' =>
                $moneda->simbolo,

            'valor_convertido' =>
                $valorConvertido,

            'tasa' =>
                $tasa['tasa'],

            'fecha_tasa' =>
                $tasa['fecha'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 9. Respuesta
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'data' => [

                'pais' =>
                    $consulta->pais,

                'ciudad' =>
                    $consulta->ciudad,

                'presupuesto_cop' =>
                    $consulta->presupuesto_cop,

                'clima_c' =>
                    $consulta->clima_c,

                'moneda' =>
                    $consulta->moneda,

                'simbolo' =>
                    $consulta->simbolo_moneda,

                'valor_convertido' =>
                    $consulta->valor_convertido,

                'tasa_aplicada' =>
                    $consulta->tasa,

                'fecha_tasa' =>
                    $consulta->fecha_tasa->format('Y-m-d'),
            ]

        ], 201);
    }


    /**
     * GET /api/consultas/historial
     */
    public function historial(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Obtener usuario del token
        |--------------------------------------------------------------------------
        */

        $usuario = $request->attributes->get('usuario');

        if (!$usuario) {

            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Obtener las últimas 5 consultas
        |    SOLO del usuario autenticado
        |--------------------------------------------------------------------------
        */

        $historial = Consulta::where(
            'user_id',
            $usuario->id
        )
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. Responder
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'data' => $historial
        ]);
    }
}