<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_ministerio' => 'required|exists:ministerios,id',
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'lugar' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|string|in:Programado,En Progreso,Completado,Cancelado',
        ];
    }
}
