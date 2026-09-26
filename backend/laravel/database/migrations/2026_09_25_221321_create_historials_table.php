<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ciudad_id')->constrained('ciudades')->onDelete('cascade');
            $table->decimal('presupuesto_cop', 15, 2);
            $table->string('clima')->nullable();
            $table->decimal('tasa', 15, 4);
            $table->decimal('valor_convertido', 15, 2);
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial');
    }
};
