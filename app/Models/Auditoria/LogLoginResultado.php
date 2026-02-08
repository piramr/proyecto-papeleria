<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class LogLoginResultado extends Model
{
    protected $table = 'log_login_resultados';
    protected $fillable = ['nombre', 'description'];
}