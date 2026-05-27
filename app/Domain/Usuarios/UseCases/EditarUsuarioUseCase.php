<?php

namespace App\Domain\Usuarios\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Usuarios\Repositories\UsuarioRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EditarUsuarioUseCase
{
    public function __construct(private UsuarioRepository $repo) {}

    public function ejecutar(int $userId, array $datos): User
    {
        $user = $this->repo->obtenerPorId($userId);
        if (! $user) {
            throw (new ModelNotFoundException())->setModel(User::class, [$userId]);
        }

        // Unicidad de email/CI excepto el propio
        if (isset($datos['email']) && $this->repo->emailExiste($datos['email'], $userId)) {
            throw ValidationException::withMessages([
                'email' => 'Ya existe otro usuario con ese email.',
            ]);
        }

        if (! empty($datos['ci']) && $this->repo->ciExiste($datos['ci'], $userId)) {
            throw ValidationException::withMessages([
                'ci' => 'Ya existe otro usuario con ese CI.',
            ]);
        }

        // Password: hashear si vino con valor; quitarlo si vino vacío/null
        if (array_key_exists('password', $datos)) {
            if (! empty($datos['password'])) {
                $datos['password'] = Hash::make($datos['password']);
            } else {
                unset($datos['password']);
            }
        }

        // REGLA CRÍTICA: no quitar el rol Administrador al último admin activo
        if (isset($datos['rol_id']) && (int) $datos['rol_id'] !== (int) $user->rol_id) {
            $esAdminActual = $user->rol && $user->rol->nombre === 'Administrador';
            if ($esAdminActual && $user->activo && $this->repo->contarAdministradoresActivos() === 1) {
                throw ValidationException::withMessages([
                    'rol_id' => 'No se puede quitar el rol de Administrador al último administrador activo del sistema.',
                ]);
            }
        }

        // Detectar campos cambiados (para la bitácora), sin incluir password en claro
        $cambiados = [];
        foreach ($datos as $campo => $valor) {
            if ($campo === 'password') {
                $cambiados[] = 'password';
                continue;
            }
            if ((string) $user->{$campo} !== (string) $valor) {
                $cambiados[] = $campo;
            }
        }

        $user->update($datos);
        $user->load('rol');

        BitacoraLogger::registrar(
            'USUARIO_EDITADO',
            'Seguridad',
            'Usuario editado: '.$user->email.' (campos: '.(empty($cambiados) ? 'ninguno' : implode(', ', $cambiados)).')'
        );

        return $user;
    }
}
