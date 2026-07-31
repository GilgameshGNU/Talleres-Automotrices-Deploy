<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    public $timestamps = false;
    protected $table = 'pago';
    protected $primaryKey = 'idPago';
    
    protected $fillable = [
        'idFactura',
        'idMetodoPago',
        'monto',
        'fecha',
        'estado'
    ];
}
