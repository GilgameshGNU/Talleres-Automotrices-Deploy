<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'idAuditoria';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'fechaHora',
        'accion',
        'tablaAfectada',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }
}
