<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoHistorico extends Model
{
    use HasFactory;

    protected $table = 'pedido_historicos';

    protected $fillable = [
        'pedido_id',
        'tipo_status_id',
        'user_id',
        'observacao',
        'notificacao_enviada',
    ];

    protected $casts = [
        'notificacao_enviada' => 'boolean',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function tipoStatus()
    {
        return $this->belongsTo(TipoStatus::class, 'tipo_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}