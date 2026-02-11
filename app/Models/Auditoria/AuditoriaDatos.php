<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class AuditoriaDatos extends Model
{
    protected $table = 'auditoria_datos';
    protected $fillable = [
        'timestamp', 'user_id', 'session_id', 'tipo_operacion', 'entidad', 'recurso_id', 'recurso_padre_id', 'campo', 'valor_original', 'valor_nuevo'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}