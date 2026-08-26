<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MiembroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $miembroId = $this->route('miembro') ? $this->route('miembro')->id : null;

        return [
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|unique:miembros,email,' . $miembroId,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'f_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|max:10',
            'fecha_ingreso' => 'required|date',
            'estado' => 'required|string|max:20',
        ];
    }
}
