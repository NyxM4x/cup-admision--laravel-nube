<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'ci', 'nombre', 'fecha_nacimiento', 'sexo',
        'direccion', 'telefono', 'correo', 'foto',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function docente()
    {
        return $this->hasOne(Docente::class);
    }
}