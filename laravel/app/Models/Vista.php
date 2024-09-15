<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vista extends Model
{
    use HasFactory;

    protected $table = 'vistas';

    protected $fillable = [
        'propuesta_id',
        'URL_imagenes',
        'fecha_envio',
        'descripcion'
    ];


    public function propuesta()
    {
        return $this->belongsTo(Proyecto::class, 'propuesta_id');
    }
}
