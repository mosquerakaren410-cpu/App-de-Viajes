<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Illuminate\Encryption\Encrypter;


class TokenService {
    public function generarAccessToken($user) {
        $secreto = base64_decode(env("APP_TOKEN_SECRET"));

        $claveFirma = hash_hkdf(
            "sha256",
            $secreto,
            32,
            "jwt-firma"
        );

        $claveCifrado = hash_hkdf(
            "sha256",
            $secreto,
            32,
            "jwt-cifrado"
        );

        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 900,
            'jti' => Str::uuid()->toString(),
            'idioma' => $user->idioma
        ];

        $jwt = JWT::encode(
            $payload,
            $claveFirma,
            'HS256'
        );

        $encrypter = new Encrypter(
            $claveCifrado,
            'AES-256-GCM'
        );

        return $encrypter->encryptString($jwt);
    }

    public function generarRefreshToken() {
        return Str::random(120);
    }

    public function validarAccessToken(string $token) {
        
        $secreto = base64_decode(env("APP_TOKEN_SECRET"));

        $claveFirma = hash_hkdf(
            "sha256",
            $secreto,
            32,
            "jwt-firma"
        );

        $claveCifrado = hash_hkdf(
            "sha256",
            $secreto,
            32,
            "jwt-cifrado"
        );

        $encrypter = new Encrypter(
            $claveCifrado,
            'AES-256-GCM'
        );

        $jwt = $encrypter->decryptString($token);

        return JWT::decode(
            $jwt,
            new Key($claveFirma, 'HS256')
        );

    }
}