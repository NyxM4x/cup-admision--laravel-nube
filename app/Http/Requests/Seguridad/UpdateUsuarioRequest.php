<?php

namespace App\Http\Requests\Seguridad;

use App\Rules\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
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
        // {usuario} es el id (sin route-model binding, el método recibe int)
        $usuarioId = $this->route('usuario');

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuarioId)],
            'password' => ['nullable', PasswordValidationRules::password(), 'confirmed'],
            'ci'       => ['nullable', 'string', 'max:20', Rule::unique('users', 'ci')->ignore($usuarioId)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol_id'   => ['required', 'exists:roles,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'El email no tiene un formato válido.',
            'email.unique'       => 'Ya existe otro usuario con ese email.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.mixed_case' => 'La contraseña debe incluir al menos una letra mayúscula y una letra minúscula.',
            'password.symbols'   => 'La contraseña debe incluir al menos un símbolo.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'ci.unique'          => 'Ya existe otro usuario con ese CI.',
            'rol_id.required'    => 'Debe seleccionar un rol.',
            'rol_id.exists'      => 'El rol seleccionado no es válido.',
        ];
    }
}
