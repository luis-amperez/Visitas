<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'visitas';
    protected $fillable = [
        'correo_cliente',
        'nombre_cliente',
        'id_cliente',
        'nombre_tecnico',
        'id_tecnico',
        'fecha_inicio',
        'fecha_final',

    ];
}
