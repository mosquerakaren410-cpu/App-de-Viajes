<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
        'nombre' => 'required|string|max:255',
        'correo' => 'required|email',
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->numbers()
        ]
    ]);

    $existe = User::where('correo', $request->correo)->first();

    if ($existe) {
        return response()->json([
            'success' => false,
            'code' => 'USER_ALREADY_EXISTS'
        ], 409);
    }

    $usuario = User::create([
        'nombre' => $request->nombre,
        'correo' => $request->correo,
        'password_hash' => Hash::make($request->password),
        'idioma' => 'es'
    ]);

    return response()->json([
        'success' => true,
        'data' => $usuario
    ], 201);
    }


}
