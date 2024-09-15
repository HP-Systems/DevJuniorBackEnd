<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'fecha_nacimiento',
        'universidad',
        'clave_estudiante',
        'periodo',
        'n_periodo',
        'foto_credencial'
    ];
}
