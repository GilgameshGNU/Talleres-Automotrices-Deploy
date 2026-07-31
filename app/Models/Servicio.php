<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Servicio extends Model
{
    protected $table = 'servicio';
    protected $primaryKey = 'idServicio';
    public $timestamps = false;

    protected $fillable = [
        'idTipoServicio',
        'nombre',
        'precioBase',
        'duracionEstimada',
    ];

    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'idTipoServicio', 'idTipoServicio');
    }
}
