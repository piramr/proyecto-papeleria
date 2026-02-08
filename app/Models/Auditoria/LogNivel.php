<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class LogNivel extends Model
{
    protected $table = 'log_nivel';
    protected $fillable = ['nombre', 'descripcion'];
}