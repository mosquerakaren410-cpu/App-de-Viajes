<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $fillable = [
        'user_id',
        'ciudad_id',
        'pais',
        'ciudad',
        'presupuesto_cop',
        'clima_c',
        'moneda',
        'simbolo_moneda',
        'valor_convertido',
        'tasa',
        'fecha_tasa',
    ];

    protected $casts = [
        'presupuesto_cop' => 'decimal:2',
        'clima_c' => 'decimal:2',
        'valor_convertido' => 'decimal:2',
        'tasa' => 'decimal:8',
        'fecha_tasa' => 'date:Y-m-d',
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}