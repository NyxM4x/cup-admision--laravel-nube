<?php

namespace App\Http\Requests\Seguridad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends FormRequest
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
        $rolId = $this->route('rol');

        return [
            'nombre'      => ['required', 'string', 'max:50', Rule::unique('roles', 'nombre')->ignore($rolId)],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'permisos'    => ['nullable', 'array'],
            'permisos.*'  => ['integer', 'exists:permisos,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre del rol es obligatorio.',
            'nombre.max'        => 'El nombre no puede superar los 50 caracteres.',
            'nombre.unique'     => 'Ya existe otro rol con ese nombre.',
            'descripcion.max'   => 'La descripción no puede superar los 500 caracteres.',
            'permisos.array'    => 'Los permisos enviados no son válidos.',
            'permisos.*.exists' => 'Uno de los permisos seleccionados no existe.',
        ];
    }
}
