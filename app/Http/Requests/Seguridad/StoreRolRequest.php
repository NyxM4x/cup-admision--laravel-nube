<?php

namespace App\Http\Requests\Seguridad;

use Illuminate\Foundation\Http\FormRequest;

class StoreRolRequest extends FormRequest
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
            'nombre'      => ['required', 'string', 'max:50', 'unique:roles,nombre'],
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
            'nombre.unique'     => 'Ya existe un rol con ese nombre.',
            'descripcion.max'   => 'La descripción no puede superar los 500 caracteres.',
            'permisos.array'    => 'Los permisos enviados no son válidos.',
            'permisos.*.exists' => 'Uno de los permisos seleccionados no existe.',
        ];
    }
}
