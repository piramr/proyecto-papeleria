<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DdlAuditoria extends Model
{
    protected $table = 'ddl_auditoria';
    public $timestamps = false;
    protected $fillable = ['user_id', 'ddl_fecha', 'evento', 'objeto_tipo', 'objeto_nombre', 'esquema', 'sql_command', 'tipo_log_id'];

    protected $casts = [
        'ddl_fecha' => 'datetime',
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
