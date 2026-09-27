<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambio extends Model
{
    protected $table = 'tasas_cambio';

    protected $fillable = [
        'origen',
        'destino',
        'tasa',
        'fecha'
    ];

}
