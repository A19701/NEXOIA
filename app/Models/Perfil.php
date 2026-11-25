<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfiles';

    protected $fillable = [
        'user_id',
        'ocupacion',
        'intereses',
        'preferencias',
        'contexto_personal',
        'memoria_activa'
    ];

    protected $casts = [
        'memoria_activa' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}