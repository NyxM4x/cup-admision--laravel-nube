<?php

namespace App\Http\Requests\Seguridad;

use App\Rules\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', PasswordValidationRules::password(), 'confirmed'],
            'ci'       => ['nullable', 'string', 'max:20', 'unique:users,ci'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol_id'   => ['required', 'exists:roles,id'],
            'activo'   => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'name.max'           => 'El nombre no puede superar los 150 caracteres.',
            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'El email no tiene un formato válido.',
            'email.unique'       => 'Ya existe un usuario con ese email.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed_case' => 'La contraseña debe incluir al menos una letra mayúscula y una letra minúscula.',
            'password.symbols'   => 'La contraseña debe incluir al menos un símbolo.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'ci.unique'          => 'Ya existe un usuario con ese CI.',
            'rol_id.required'    => 'Debe seleccionar un rol.',
            'rol_id.exists'      => 'El rol seleccionado no es válido.',
        ];
    }
}
