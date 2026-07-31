<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table = 'tiposervicio';
    protected $primaryKey = 'idTipoServicio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'idTipoServicio', 'idTipoServicio');
    }
}
