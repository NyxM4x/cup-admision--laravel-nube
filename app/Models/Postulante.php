<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    protected $table = 'postulantes';

    protected $fillable = [
        'persona_id',
        'colegio',
        'estado',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    // Inscripción del periodo activo
    public function inscripcionActiva()
    {
        return $this->hasOne(Inscripcion::class)->where('estado', 'activa');
    }
}