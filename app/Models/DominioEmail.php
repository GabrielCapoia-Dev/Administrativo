<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class DominioEmail extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $table = 'dominio_emails';

    protected $fillable = [
        'dominio_email',
        'setor',
        'status',
        'ativo',
        'registro_anterior_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['dominio_email', 'setor', 'status', 'ativo']);
    }

    public function registroAnterior()
    {
        return $this->belongsTo(DominioEmail::class, 'registro_anterior_id');
    }

    public function historico()
    {
        return $this->hasMany(DominioEmail::class, 'registro_anterior_id');
    }
}