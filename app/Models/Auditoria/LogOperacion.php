<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogOperacion extends Model
{
    protected $table = 'log_operacion';
    protected $fillable = [
        'timestamp', 'user_id', 'session_id', 'ip_address', 'tipo_operacion', 'entidad', 'recurso_id', 'recurso_padre_id', 'resultado', 'codigo_error', 'mensaje_error'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}