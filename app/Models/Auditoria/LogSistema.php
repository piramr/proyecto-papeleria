<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    protected $table = 'log_sistema';
    protected $fillable = ['timestamp', 'nivel_log_id', 'mensaje'];

    public function nivel()
    {
        return $this->belongsTo(LogNivel::class, 'nivel_log_id');
    }
}