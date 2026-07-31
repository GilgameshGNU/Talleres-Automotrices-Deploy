<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    public $timestamps = false;
    protected $table = 'factura';
    protected $primaryKey = 'idFactura';
    
    protected $fillable = [
        'idOrden',
        'nroFactura',
        'fechaEmision',
        'montoTotal',
        'nitCliente'
    ];
}
