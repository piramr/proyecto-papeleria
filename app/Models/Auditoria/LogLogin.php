<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class LogLogin extends Model
{
    protected $table = 'log_login';
    protected $fillable = [
        'timestamp', 'user_email', 'user_id', 'host', 'reintento', 'dispositivo', 'ubicacion', 'resultado_log_id'
    ];

    public function resultado()
    {
        return $this->belongsTo(LogLoginResultado::class, 'resultado_log_id');
    }
}