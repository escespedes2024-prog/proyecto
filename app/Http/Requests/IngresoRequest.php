<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_actividad' => 'nullable|exists:actividades,id',
            'id_culto' => 'nullable|exists:cultos,id',
            'monto_total' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'tipo' => 'required|string|max:100',
            'metodo_pago' => 'required|string|max:100',
        ];
    }
}
