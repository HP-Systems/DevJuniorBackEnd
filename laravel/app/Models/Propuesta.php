<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    use HasFactory;

    
    protected $table = 'propuestas';

    protected $fillable = [
        'estudiante_id',
        'proyecto_id',
        'descripcion',
        'id_mongo',
        'etapa',
        'fecha_envio',
        'URL_propuesta',
        'id_mongo',
        'status'
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function vista()
    {
        return $this->hasMany(Vista::class, 'proyecto_id');
    }

    
}
