<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodopago';
    protected $primaryKey = 'idMetodoPago';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'comision',
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'idMetodoPago', 'idMetodoPago');
    }
}
