<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('ciudad_id')
                ->constrained('ciudades')
                ->cascadeOnDelete();

            $table->string('pais');
            $table->string('ciudad');

            $table->decimal('presupuesto_cop', 15, 2);

            $table->decimal('clima_c', 8, 2);

            $table->string('moneda');
            $table->string('simbolo_moneda', 10);

            $table->decimal('valor_convertido', 15, 2);

            $table->decimal('tasa', 15, 8);

            $table->date('fecha_tasa');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};