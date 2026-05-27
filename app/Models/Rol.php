<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: un rol tiene muchos permisos (pivot rol_permiso)
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id');
    }

    // Relación: un rol tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
