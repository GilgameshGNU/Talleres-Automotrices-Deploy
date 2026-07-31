<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';
    protected $primaryKey = 'idInventario';
    public $timestamps = false;

    protected $fillable = [
        'idSucursal',
        'idRepuesto',
        'stockActual',
        'stockMinimo',
        'ubicacion',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idSucursal', 'idSucursal');
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'idRepuesto', 'idRepuesto');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'idInventario', 'idInventario');
    }
}
