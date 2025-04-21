<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // La tabla asociada a este modelo
    protected $table = 'plans';

    // Los atributos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'description',
        'price',
        'features',
    ];

    // Indicamos que 'features' es un campo JSON
    protected $casts = [
        'features' => 'array', // Convierte el campo 'features' a un arreglo automáticamente
    ];

    // Si se quiere personalizar las fechas de creación y actualización
    protected $dates = ['created_at', 'updated_at'];
}
