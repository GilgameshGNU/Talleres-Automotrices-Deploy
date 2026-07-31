<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = 'repuesto';
    protected $primaryKey = 'idRepuesto';
    public $timestamps = false;

    protected $fillable = [
        'idProveedor',
        'codigo',
        'nombre',
        'precioVenta',
        'marca',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'idProveedor', 'idProveedor');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'idRepuesto', 'idRepuesto');
    }
}
