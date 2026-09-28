<?php

namespace Tests\Unit;

use App\Models\TasaCambio;
use App\Services\MonedaServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MonedaServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_when_the_currency_api_fails_the_last_saved_rate_is_used(): void
    {
        TasaCambio::create([
            'origen' => 'COP',
            'destino' => 'JPY',
            'tasa' => 0.0047,
            'fecha' => '2026-09-27',
        ]);

        Http::fake([
            '*' => Http::response(['error' => 'service unavailable'], 503),
        ]);

        $result = app(MonedaServices::class)->obtenerTasa('JPY');

        $this->assertTrue($result['success']);
        $this->assertSame(0.0047, (float) $result['tasa']);
        $this->assertSame('2026-09-27', $result['fecha']);
        $this->assertSame('cache', $result['source']);
    }
}
