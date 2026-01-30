<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pedido extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'pedidos';

    protected $fillable = [
        'numero_protocolo',
        'descricao',
        'tipo_manutencao_id',
        'tipo_status_id',
        'escola_id',
        'solicitante_id',
        'responsavel_educacao_id',
        'responsavel_obras_id',
        'data_prevista',
        'data_entrega',
        'data_cancelamento',
        'quantidade_dias_prorrogado',
        'ativo',
        'registro_anterior_id',
    ];

    protected $casts = [
        'data_prevista' => 'date',
        'data_entrega' => 'date',
        'data_cancelamento' => 'date',
        'ativo' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'numero_protocolo',
                'descricao',
                'tipo_manutencao_id',
                'tipo_status_id',
                'responsavel_educacao_id',
                'responsavel_obras_id',
                'data_prevista',
                'data_entrega',
                'ativo',
            ]);
    }

    protected static function booted()
    {
        static::creating(function ($pedido) {
            if (empty($pedido->numero_protocolo)) {
                $pedido->numero_protocolo = self::gerarProtocolo();
            }
        });
    }

    public static function gerarProtocolo(): string
    {
        $ano = date('Y');
        $ultimo = self::whereYear('created_at', $ano)
            ->where('ativo', true)
            ->count();
        
        return sprintf('%s/%05d', $ano, $ultimo + 1);
    }

    // Relacionamentos
    public function tipoManutencao()
    {
        return $this->belongsTo(TipoManutencao::class, 'tipo_manutencao_id');
    }

    public function tipoStatus()
    {
        return $this->belongsTo(TipoStatus::class, 'tipo_status_id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'escola_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function responsavelEducacao()
    {
        return $this->belongsTo(User::class, 'responsavel_educacao_id');
    }

    public function responsavelObras()
    {
        return $this->belongsTo(User::class, 'responsavel_obras_id');
    }

    public function fotos()
    {
        return $this->hasMany(PedidoFoto::class, 'pedido_id');
    }

    public function historicos()
    {
        return $this->hasMany(PedidoHistorico::class, 'pedido_id')->orderBy('created_at', 'desc');
    }

    public function registroAnterior()
    {
        return $this->belongsTo(Pedido::class, 'registro_anterior_id');
    }

    public function historicoRegistro()
    {
        return $this->hasMany(Pedido::class, 'registro_anterior_id');
    }
}