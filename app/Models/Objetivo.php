<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $table = 'objetivos';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'categoria',
        'estado',
        'progreso',
        'fecha_limite'
    ];

    protected $casts = [
        'fecha_limite' => 'date',
        'progreso' => 'integer'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}