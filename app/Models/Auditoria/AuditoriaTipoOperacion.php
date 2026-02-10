<?php
namespace App\Models\Auditoria;

use Illuminate\Database\Eloquent\Model;

class AuditoriaTipoOperacion extends Model
{
    protected $table = 'auditoria_tipo_operacion';
    protected $fillable = ['nombre', 'descripcion'];
}
