<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserLoginAttemptsLog extends Model
{
    protected $table = 'user_login_attempts_log';
    public $timestamps = false;
    protected $fillable = ['user_id', 'username_attempted', 'host', 'attempt_fecha', 'result', 'failure_reason', 'tipo_log_id'];

    protected $casts = [
        'attempt_fecha' => 'datetime',
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
