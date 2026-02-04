<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DmlAuditoria extends Model
{
    protected $table = 'dml_auditoria';
    public $timestamps = false;
    protected $fillable = ['user_id', 'accion', 'timestamp', 'esquema', 'tabla', 'columna', 'valor_anterior', 'valor_nuevo', 'fila_id', 'transaccion_id', 'tipo_log_id'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoLog()
    {
        return $this->belongsTo(TipoLog::class);
    }
}
