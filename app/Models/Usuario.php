<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'idUsuario';
    public $timestamps = true;

    protected $fillable = [
        'idRol',
        'nombreUsuario',
        'contrasena',
        'email',
        'estado',
        'created_at',
    ];

    protected $hidden = [
        'contrasena',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Get the name of the password field.
     */
    public function getAuthPasswordName()
    {
        return 'contrasena';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'idUsuario', 'idUsuario');
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'idUsuario', 'idUsuario');
    }
}
