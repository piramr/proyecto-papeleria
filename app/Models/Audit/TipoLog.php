<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class TipoLog extends Model
{
    protected $table = 'tipos_log';
    public $timestamps = false;
    protected $fillable = ['codigo', 'nombre', 'descripcion'];
}
