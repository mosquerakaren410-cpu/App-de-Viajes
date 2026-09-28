<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\Moneda;
use App\Models\Pais;
use App\Models\User;
use App\Services\ClimaServices;
use App\Services\MonedaServices;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'nombre' => 'Usuario de prueba',
            'correo' => 'consulta@example.com',
            'password_hash' => password_hash('Secret123', PASSWORD_BCRYPT),
            'idioma' => 'es',
        ]);
    }

    private function createCity(): Ciudad
    {
        $moneda = Moneda::create([
            'codigo' => 'JPY',
            'nombre' => 'Yen',
            'simbolo' => '¥',
        ]);

        $pais = Pais::create([
            'nombre' => 'Japon',
            'codigo' => 'JPY',
            'moneda_id' => $moneda->id,
        ]);

        return Ciudad::create([
            'pais_id' => $pais->id,
            'nombre' => 'Tokio',
            'latitud' => 35.6762,
            'longitud' => 139.6503,
        ]);
    }

    private function authToken(User $user): string
    {
        return app(TokenService::class)->generarAccessToken($user);
    }

    public function test_empty_negative_and_text_budget_are_rejected_with_field_details(): void
    {
        $user = $this->createUser();
        $city = $this->createCity();
        $token = $this->authToken($user);

        foreach (['', -100, 'abc'] as $budget) {
            $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                ->postJson('/api/consultas', [
                    'ciudad_id' => $city->id,
                    'presupuesto' => $budget,
                ]);

            $response->assertStatus(422)
                ->assertJsonPath('error.code', 'VALIDATION_ERROR');

            $details = $response->json('error.details');
            $this->assertTrue(collect($details)->contains(fn ($detail) => $detail['field'] === 'presupuesto'));
        }
    }

    public function test_currency_conversion_uses_the_mocked_rate(): void
    {
        $user = $this->createUser();
        $city = $this->createCity();
        $token = $this->authToken($user);

        $this->mock(ClimaServices::class, function ($mock) {
            $mock->shouldReceive('obtenerClima')
                ->once()
                ->andReturn([
                    'success' => true,
                    'temperatura' => 22.5,
                ]);
        });

        $this->mock(MonedaServices::class, function ($mock) {
            $mock->shouldReceive('obtenerTasa')
                ->once()
                ->with('JPY')
                ->andReturn([
                    'success' => true,
                    'tasa' => 0.0045,
                    'fecha' => '2026-09-28',
                    'source' => 'mock',
                ]);
        });

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/consultas', [
                'ciudad_id' => $city->id,
                'presupuesto' => 500000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.pais', 'Japon')
            ->assertJsonPath('data.ciudad', 'Tokio')
            ->assertJsonPath('data.tasa_aplicada', '0.00450000');

        $this->assertSame(2250.0, (float) $response->json('data.valor_convertido'));
    }
}
