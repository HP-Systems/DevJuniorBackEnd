<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_creacion',
        'fecha_limite',
        'status',
        'empresa_id'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function propuesta()
    {
        return $this->hasMany(Propuesta::class, 'proyecto_id');
    }


    public function vista()
    {
        return $this->hasMany(Vista::class, 'proyecto_id');
    }
}
