<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloVehiculo extends Model
{
    protected $table = 'modelovehiculo';
    protected $primaryKey = 'idModelo';
    public $timestamps = false;

    protected $fillable = [
        'idMarca',
        'nombre',
        'tipoMotor',
    ];

    public function marca()
    {
        return $this->belongsTo(MarcaVehiculo::class, 'idMarca', 'idMarca');
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'idModelo', 'idModelo');
    }
}
