<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesion extends Model
{
    protected $table = 'profesiones';

    protected $fillable = ['nombre', 'nivel_jerarquico'];

    public function docentes()
    {
        return $this->hasMany(Docente::class);
    }
}