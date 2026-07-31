<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidad';
    protected $primaryKey = 'idEspecialidad';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function mecanicos()
    {
        return $this->belongsToMany(Mecanico::class, 'mecanico_especialidad', 'idEspecialidad', 'idMecanico');
    }
}
