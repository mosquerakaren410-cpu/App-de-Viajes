<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Consulta;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = [
        'pais_id',
        'nombre',
        'latitud',
        'longitud'
    ];

    
    public function pais() {
        
        return $this->belongsTo(Pais::class);
        
        }

    public function consultas() {

            return $this->hasMany(Consulta::class);

        }

}
        