<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'idNotificacion';
    
    protected $fillable = [
        'idOrden',
        'idUsuario',
        'mensaje',
        'leido',
        'fecha'
    ];
}
