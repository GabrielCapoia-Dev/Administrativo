<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Escola extends Model
{
    use HasFactory;
    use Notifiable;
    use LogsActivity;

    protected $table = 'escolas';

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'ativo',
        'registro_anterior_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nome', 'telefone', 'email', 'ativo']);
    }

    // Relacionamentos
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_escola');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'escola_id');
    }

    // Histórico
    public function registroAnterior()
    {
        return $this->belongsTo(Escola::class, 'registro_anterior_id');
    }

    public function historico()
    {
        return $this->hasMany(Escola::class, 'registro_anterior_id');
    }
}