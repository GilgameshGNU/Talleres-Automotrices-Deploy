<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'idCliente';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'nombreCompleto',
        'ci_nit',
        'telefono',
        'direccion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'idCliente', 'idCliente');
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'idCliente', 'idCliente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }
}
