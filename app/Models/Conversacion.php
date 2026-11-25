<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    protected $table = 'conversaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'categoria'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'conversacion_id');
    }
}