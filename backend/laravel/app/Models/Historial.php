<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{

    protected $table = 'historial';

    protected $fillable = [

        'usuario_id',
        'ciudad_id',
        'presupuesto_cop',
        'clima',
        'tasa',
        'valor_convertido',
        'fecha'

    ];

    public function ciudad()
{
    return $this->belongsTo(Ciudad::class);
}

    public function usuario()
{
    return $this->belongsTo(User::class, 'usuario_id');
}


}
