<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vista extends Model
{
    use HasFactory;

    protected $table = 'vistas';

    protected $fillable = [
        'proyecto_id',
        'URL_imagenes',
        'fecha_envio',
        'descripcion'
    ];


    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}
