<?php

namespace App\Http\Controllers;

use App\Models\Pais;

class PaisController extends Controller
{
    public function index() {
        return response()->json([

            'success' => true,
            'data' => Pais::all()

        ]);
    }

    public function ciudades($id) {

        $pais = Pais::find($id);

        if(!$pais) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'data' => $pais->ciudades
        ]);

    }
}



