<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoFoto extends Model
{
    use HasFactory;

    protected $table = 'pedido_fotos';

    protected $fillable = [
        'pedido_id',
        'caminho',
        'nome_original',
        'descricao',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}