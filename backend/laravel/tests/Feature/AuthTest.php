<?php

namespace Tests\Feature;

use App\Models\RefreshToken;
use App\Models\TokenRevocado;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'nombre' => 'Usuario de prueba',
            'correo' => 'test@example.com',
            'password_hash' => password_hash('Secret123', PASSWORD_BCRYPT),
            'idioma' => 'es',
        ]);
    }

    private function tokenFor(User $user): string
    {
        return app(TokenService::class)->generarAccessToken($user);
    }

    public function test_a_valid_token_is_accepted_and_returns_the_correct_user(): void
    {
        $user = $this->createUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/test');

        $response->assertOk()->assertJson([
            'ok' => true,
            'user_id' => $user->id,
            'correo' => $user->correo,
        ]);
    }

    public function test_a_token_with_one_changed_character_is_rejected_as_invalid(): void
    {
        $user = $this->createUser();
        $token = $this->tokenFor($user);
        $index = strlen($token) - 1;
        $replacement = $token[$index] === 'A' ? 'B' : 'A';
        $altered = substr($token, 0, $index) . $replacement;

        $response = $this->withHeader('Authorization', 'Bearer ' . $altered)
            ->getJson('/api/test');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'AUTH_TOKEN_INVALID');
    }

    public function test_a_token_created_with_another_secret_is_rejected(): void
    {
        $user = $this->createUser();
        $token = $this->tokenFor($user);

        config(['tokens.secret' => base64_encode('abcdefghijklmnopqrstuvwxyz123456')]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/test');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'AUTH_TOKEN_INVALID');
    }

    public function test_an_expired_token_is_rejected(): void
    {
        $user = $this->createUser();
        $expiredToken = app(TokenService::class)->generarAccessToken($user, -1);

        $response = $this->withHeader('Authorization', 'Bearer ' . $expiredToken)
            ->getJson('/api/test');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'AUTH_TOKEN_EXPIRED');
    }

    public function test_a_token_is_rejected_after_logout(): void
    {
        $user = $this->createUser();
        $accessToken = $this->tokenFor($user);

        $refresh = RefreshToken::create([
            'user_id' => $user->id,
            'token' => app(TokenService::class)->generarRefreshToken(),
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        $logout = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->postJson('/api/auth/logout');

        $logout->assertOk();
        $this->assertDatabaseHas('tokens_revocados', ['jti' => $this->payloadJti($accessToken)]);
        $this->assertDatabaseHas('refresh_tokens', ['id' => $refresh->id, 'revoked' => 1]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->getJson('/api/test');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'AUTH_TOKEN_REVOKED');
    }

    public function test_reusing_a_refresh_token_rejects_it_and_closes_the_users_refresh_sessions(): void
    {
        $user = $this->createUser();
        $refreshToken = app(TokenService::class)->generarRefreshToken();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);

        $first = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        $first->assertOk();
        $newRefreshToken = $first->json('data.refresh_token');
        $this->assertNotSame($refreshToken, $newRefreshToken);

        $second = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        $second->assertStatus(401)
            ->assertJsonPath('error.code', 'AUTH_REFRESH_REUSE_DETECTED');

        $this->assertSame(0, RefreshToken::where('user_id', $user->id)->where('revoked', false)->count());
    }

    public function test_wrong_password_returns_401_and_the_sixth_attempt_returns_429(): void
    {
        $user = $this->createUser();
        $key = 'login:' . strtolower($user->correo) . '|127.0.0.1';
        RateLimiter::clear($key);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = $this->postJson('/api/auth/login', [
                'correo' => $user->correo,
                'password' => 'WrongPassword123',
            ]);

            $response->assertStatus(401)
                ->assertJsonPath('error.code', 'INVALID_CREDENTIALS');
        }

        $sixth = $this->postJson('/api/auth/login', [
            'correo' => $user->correo,
            'password' => 'WrongPassword123',
        ]);

        $sixth->assertStatus(429)
            ->assertJsonPath('error.code', 'AUTH_RATE_LIMITED');
    }

    private function payloadJti(string $token): string
    {
        return app(TokenService::class)->validarAccessToken($token)->jti;
    }
}
