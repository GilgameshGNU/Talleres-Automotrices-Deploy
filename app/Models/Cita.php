<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'cita';
    protected $primaryKey = 'idCita';
    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'idVehiculo',
        'idMecanico',
        'fecha',
        'hora',
        'estado',
        'motivo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'idVehiculo', 'idVehiculo');
    }

    public function mecanico()
    {
        return $this->belongsTo(Mecanico::class, 'idMecanico', 'idMecanico');
    }
}
