<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class OrdenTrabajo extends Model
{
    protected $table = 'ordentrabajo';
    protected $primaryKey = 'idOrden';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'idVehiculo',
        'idMecanico',
        'fechaIngreso',
        'fechaEntrega',
        'estado',
        'diagnostico',
        'total',
        'horaInicio',
        'horaFinEstimada',
        'horaFinReal',
        'etapa',
        'sucursal',
        'servicioSolicitado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function mecanico()
    {
        return $this->belongsTo(Mecanico::class, 'idMecanico', 'idMecanico');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'idVehiculo', 'idVehiculo');
    }
}
