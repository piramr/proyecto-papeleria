<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserRecursosLog extends Model
{
    protected $table = 'user_recursos_log';
    public $timestamps = false;
    protected $fillable = ['user_id', 'endpoint', 'http_method', 'request_body', 'response_code', 'response_time_ms', 'timestamp', 'ip_address', 'user_agent', 'tipo_log_id'];

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
