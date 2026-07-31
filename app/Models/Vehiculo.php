<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculo';
    protected $primaryKey = 'idVehiculo';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'idModelo',
        'placa',
        'anio',
        'color',
        'kilometraje',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloVehiculo::class, 'idModelo', 'idModelo');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'idVehiculo', 'idVehiculo');
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'idVehiculo', 'idVehiculo');
    }
}
