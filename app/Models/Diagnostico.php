<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnostico';
    protected $primaryKey = 'idDiagnostico';
    
    protected $fillable = [
        'idOrden',
        'descripcion',
        'fecha'
    ];
}
