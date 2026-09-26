<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        // Monedas
        $gbp = DB::table('monedas')->insertGetId(['codigo' => 'GBP', 'nombre' => 'Libra esterlina', 'simbolo' => '£']);
        $jpy = DB::table('monedas')->insertGetId(['codigo' => 'JPY', 'nombre' => 'Yen', 'simbolo' => '¥']);
        $inr = DB::table('monedas')->insertGetId(['codigo' => 'INR', 'nombre' => 'Rupia india', 'simbolo' => '₹']);
        $dkk = DB::table('monedas')->insertGetId(['codigo' => 'DKK', 'nombre' => 'Corona danesa', 'simbolo' => 'kr']);
        
        // Paises
        $inglaterra = DB::table('paises')->insertGetId(['nombre' => 'Inglaterra', 'codigo' => 'GBP', 'moneda_id' => $gbp]);
        $japon = DB::table('paises')->insertGetId(['nombre' => 'Japon', 'codigo' => 'JPY', 'moneda_id' => $jpy]);
        $india = DB::table('paises')->insertGetId(['nombre' => 'India', 'codigo' => 'INR', 'moneda_id' => $inr]);
        $dinamarca = DB::table('paises')->insertGetId(['nombre' => 'Dinamarca', 'codigo' => 'DKK', 'moneda_id' => $dkk]);


        // Ciudades
        DB::table('ciudades')->insert([
            ['pais_id' => $inglaterra, 'nombre' => 'Londres', 'latitud' => 51.5074, 'longitud' => -0.1278],
            ['pais_id' => $inglaterra, 'nombre' => 'Mánchester', 'latitud' => 53.4808, 'longitud' => -2.2426],
            ['pais_id' => $japon, 'nombre' => 'Tokio', 'latitud' => 35.6762, 'longitud' => 139.6503],
            ['pais_id' => $japon, 'nombre' => 'Osaka', 'latitud' => 34.6937, 'longitud' => 135.5023],
            ['pais_id' => $india, 'nombre' => 'Nueva Delhi', 'latitud' => 28.6139, 'longitud' => 77.2090],
            ['pais_id' => $india, 'nombre' => 'Bombay', 'latitud' => 19.0760, 'longitud' => 72.8777],
            ['pais_id' => $dinamarca, 'nombre' => 'Copenhague', 'latitud' => 55.6761, 'longitud' => 12.5683],
            ['pais_id' => $dinamarca, 'nombre' => 'Aarhus', 'latitud' => 56.1629, 'longitud' => 10.2039],
        ]);


        //usuario de prueba
        DB::table('users')->insert([
            'nombre' => 'Test usuario',
            'correo' => 'test@viajes.com',
            'password_hash' => Hash::make('secret123'), 
            'idioma' => 'es'
        ]);
    }
}
