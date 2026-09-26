<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'codigo',
        'moneda_id'
    ];

    public function ciudades() {

        return $this->hasMany(
            Ciudad::class
        );

    }
}
