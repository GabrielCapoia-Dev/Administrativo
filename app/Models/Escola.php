<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    //
    protected $table = 'escolas';

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'ativo',
        'registro_anterior_id',
    ];
}
