<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenRevocado extends Model
{
    protected $table = 'tokens_revocados';

    protected $fillable = [
        'jti',
        'revoked_at'
    ];
}
