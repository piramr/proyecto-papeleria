<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserLoginAudit extends Model
{
    protected $table = 'user_login_audit';
    public $timestamps = false;
    protected $fillable = ['user_id', 'session_id', 'host', 'login_fecha', 'logout_fecha', 'duration_seconds', 'tipo_log_id'];

    protected $casts = [
        'login_fecha' => 'datetime',
        'logout_fecha' => 'datetime',
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
