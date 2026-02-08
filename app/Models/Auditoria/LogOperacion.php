<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class LogOperacion extends Model
{
    protected $table = 'log_operacion';
    protected $fillable = [
        'timestamp', 'user_id', 'session_id', 'tipo_operacion', 'entidad', 'recurso_id', 'recurso_padre_id', 'resultado', 'codigo_error', 'mensaje_error'
    ];
}