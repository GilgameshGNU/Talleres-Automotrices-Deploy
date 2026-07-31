<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcaVehiculo extends Model
{
    protected $table = 'marcavehiculo';
    protected $primaryKey = 'idMarca';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'paisOrigen',
    ];

    public function modelos()
    {
        return $this->hasMany(ModeloVehiculo::class, 'idMarca', 'idMarca');
    }
}
