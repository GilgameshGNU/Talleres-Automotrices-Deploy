<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenTrabajo extends Model
{
    public $timestamps = false;
    protected $table = 'detalleordentrabajo';
    protected $primaryKey = 'idDetalle';
    
    protected $fillable = [
        'idOrden',
        'idServicio',
        'idRepuesto',
        'cantidad',
        'precioUnitario',
        'subtotal',
        'observaciones'
    ];
}
