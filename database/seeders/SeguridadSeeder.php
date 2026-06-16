<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SeguridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Idempotente: usa firstOrCreate/updateOrCreate para poder re-ejecutarse
     * sin romper por restricciones unique.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------
        // 1. Permisos base, agrupados por módulo
        // ---------------------------------------------------------------
        $permisosData = [
            // Módulo Seguridad
            ['codigo' => 'usuarios.ver',        'modulo' => 'Seguridad',     'descripcion' => 'Ver usuarios'],
            ['codigo' => 'usuarios.crear',      'modulo' => 'Seguridad',     'descripcion' => 'Crear usuarios'],
            ['codigo' => 'usuarios.editar',     'modulo' => 'Seguridad',     'descripcion' => 'Editar usuarios'],
            ['codigo' => 'usuarios.eliminar',   'modulo' => 'Seguridad',     'descripcion' => 'Eliminar usuarios'],
            ['codigo' => 'roles.ver',           'modulo' => 'Seguridad',     'descripcion' => 'Ver roles'],
            ['codigo' => 'roles.crear',         'modulo' => 'Seguridad',     'descripcion' => 'Crear roles'],
            ['codigo' => 'roles.editar',        'modulo' => 'Seguridad',     'descripcion' => 'Editar roles'],
            ['codigo' => 'roles.eliminar',      'modulo' => 'Seguridad',     'descripcion' => 'Eliminar roles'],
            ['codigo' => 'permisos.gestionar',  'modulo' => 'Seguridad',     'descripcion' => 'Gestionar permisos de roles'],
            ['codigo' => 'bitacora.ver',        'modulo' => 'Seguridad',     'descripcion' => 'Ver bitácora del sistema'],

            // Módulo GestionGlobal
            ['codigo' => 'aulas.ver',           'modulo' => 'GestionGlobal', 'descripcion' => 'Ver aulas'],
            ['codigo' => 'aulas.crear',         'modulo' => 'GestionGlobal', 'descripcion' => 'Crear aulas'],
            ['codigo' => 'aulas.editar',        'modulo' => 'GestionGlobal', 'descripcion' => 'Editar aulas'],
            ['codigo' => 'aulas.eliminar',      'modulo' => 'GestionGlobal', 'descripcion' => 'Eliminar aulas'],
        ];

        $permisos = collect();
        foreach ($permisosData as $p) {
            $permisos->push(
                Permiso::firstOrCreate(['codigo' => $p['codigo']], $p)
            );
        }

        // ---------------------------------------------------------------
        // 2. Roles base
        // ---------------------------------------------------------------
        $administrador  = Rol::firstOrCreate(['nombre' => 'Administrador'],   ['descripcion' => 'Acceso total al sistema', 'activo' => true]);
        $coordinador    = Rol::firstOrCreate(['nombre' => 'Coordinador CUP'], ['descripcion' => 'Gestión académica del CUP', 'activo' => true]);
        Rol::firstOrCreate(['nombre' => 'Postulante'],     ['descripcion' => 'Aspirante al curso preuniversitario', 'activo' => true]);
        Rol::firstOrCreate(['nombre' => 'Docente'],        ['descripcion' => 'Docente del CUP', 'activo' => true]);
        $auditor        = Rol::firstOrCreate(['nombre' => 'Auditor'],         ['descripcion' => 'Solo lectura / auditoría', 'activo' => true]);

        // ---------------------------------------------------------------
        // 3. Asignación de permisos a roles
        // ---------------------------------------------------------------
        // Administrador: TODOS los permisos
        $administrador->permisos()->sync($permisos->pluck('id'));

        // Coordinador CUP: todos menos los de "Seguridad pura"
        // (gestión de usuarios/roles/permisos). Conserva bitacora.ver y aulas.*
        $seguridadPura = ['usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
                          'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar', 'permisos.gestionar'];
        $coordinador->permisos()->sync(
            $permisos->whereNotIn('codigo', $seguridadPura)->pluck('id')
        );

        // Auditor: solo permisos de lectura (*.ver)
        $auditor->permisos()->sync(
            $permisos->filter(fn ($p) => str_ends_with($p->codigo, '.ver'))->pluck('id')
        );

        // ---------------------------------------------------------------
        // 4. Usuario administrador de pruebas
        // ---------------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@cup.uagrm.bo'],
            [
                'name'     => 'Ariany Claure',
                'password' => Hash::make('Admin123@'),
                'activo'   => true,
                'rol_id'   => $administrador->id,
            ]
        );
    }
}
