<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientoinventario';
    protected $primaryKey = 'idMovimiento';
    public $timestamps = false;

    protected $fillable = [
        'idInventario',
        'tipo',
        'cantidad',
        'fecha',
        'motivo',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'idInventario', 'idInventario');
    }
}
