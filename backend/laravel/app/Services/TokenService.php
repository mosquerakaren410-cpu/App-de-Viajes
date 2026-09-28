<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class TokenService
{
    public function generarAccessToken(User $user, int $ttlSeconds = 900): string
    {
        $secret = $this->secret();
        $signingKey = $this->deriveKey($secret, 'jwt-firma');
        $encryptionKey = $this->deriveKey($secret, 'jwt-cifrado');

        $now = now()->timestamp;

        $payload = [
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
            'jti' => Str::uuid()->toString(),
            'idioma' => $user->idioma,
        ];

        $jwt = JWT::encode($payload, $signingKey, 'HS256');

        $encrypter = new Encrypter($encryptionKey, 'AES-256-GCM');

        return $encrypter->encryptString($jwt);
    }

    public function generarRefreshToken(): string
    {
        return Str::random(120);
    }

    public function validarAccessToken(string $token): object
    {
        $secret = $this->secret();
        $signingKey = $this->deriveKey($secret, 'jwt-firma');
        $encryptionKey = $this->deriveKey($secret, 'jwt-cifrado');

        $encrypter = new Encrypter($encryptionKey, 'AES-256-GCM');
        $jwt = $encrypter->decryptString($token);

        return JWT::decode($jwt, new Key($signingKey, 'HS256'));
    }

    private function secret(): string
    {
        $encoded = config('tokens.secret');
        $secret = base64_decode((string) $encoded, true);

        if ($secret === false || strlen($secret) !== 32) {
            throw new \RuntimeException('APP_TOKEN_SECRET debe ser una cadena Base64 de 32 bytes.');
        }

        return $secret;
    }

    private function deriveKey(string $secret, string $purpose): string
    {
        return hash_hkdf('sha256', $secret, 32, $purpose);
    }
}
