<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiderIglesiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cargo' => 'required|exists:cargos,id',
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'fecha_inicio' => 'required|date',
            'activo' => 'boolean',
        ];
    }
}
