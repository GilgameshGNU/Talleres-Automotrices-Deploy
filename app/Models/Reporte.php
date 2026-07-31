<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    public $timestamps = false;
    protected $table = 'reporte';
    protected $primaryKey = 'idReporte';
    
    protected $fillable = [
        'idUsuario',
        'tipo',
        'fechaGeneracion',
        'parametros'
    ];
}
