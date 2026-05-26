<?php

namespace App\Http\Requests\GestionGlobal;

use Illuminate\Foundation\Http\FormRequest;

class StoreAulaRequest extends FormRequest
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
            'codigo'       => ['required', 'string', 'max:20', 'unique:aulas,codigo'],
            'edificio'     => ['required', 'string', 'max:50'],
            'capacidad'    => ['required', 'integer', 'min:1', 'max:500'],
            'equipamiento' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codigo.required'    => 'El código del aula es obligatorio.',
            'codigo.max'         => 'El código no puede superar los 20 caracteres.',
            'codigo.unique'      => 'Ya existe un aula con ese código.',
            'edificio.required'  => 'El edificio es obligatorio.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer'  => 'La capacidad debe ser un número entero.',
            'capacidad.min'      => 'La capacidad debe ser mayor a 0.',
            'capacidad.max'      => 'La capacidad no puede superar los 500.',
            'equipamiento.max'   => 'El equipamiento no puede superar los 1000 caracteres.',
        ];
    }
}
