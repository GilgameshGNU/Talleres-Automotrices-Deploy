<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursal';
    protected $primaryKey = 'idSucursal';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'ciudad',
    ];

    public function mecanicos()
    {
        return $this->hasMany(Mecanico::class, 'idSucursal', 'idSucursal');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'idSucursal', 'idSucursal');
    }
}
