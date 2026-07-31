<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'idProveedor';
    public $timestamps = false;

    protected $fillable = [
        'razonSocial',
        'nit',
        'telefono',
        'email',
    ];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class, 'idProveedor', 'idProveedor');
    }
}
