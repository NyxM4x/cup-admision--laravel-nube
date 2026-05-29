<?php

namespace App\Domain\Usuarios\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Usuarios\Repositories\UsuarioRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CrearUsuarioUseCase
{
    public function __construct(private UsuarioRepository $repo) {}

    public function ejecutar(array $datos): User
    {
        // Validaciones de unicidad (defensa adicional al FormRequest)
        if ($this->repo->emailExiste($datos['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Ya existe un usuario con ese email.',
            ]);
        }

        if (! empty($datos['ci']) && $this->repo->ciExiste($datos['ci'])) {
            throw ValidationException::withMessages([
                'ci' => 'Ya existe un usuario con ese CI.',
            ]);
        }

        $datos['password'] = Hash::make($datos['password']);
        $datos['activo'] = $datos['activo'] ?? true;
        $datos['debe_cambiar_password'] = true; // Forzar cambio en primer login (politica de seguridad)

        $user = User::create($datos);
        $user->load('rol');

        BitacoraLogger::registrar(
            'USUARIO_CREADO',
            'Seguridad',
            'Usuario creado: '.$user->email.' (rol: '.($user->rol?->nombre ?? 'sin rol').')'
        );

        return $user;
    }
}
