<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EgresoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_lider_iglesia' => 'nullable|exists:lideres_iglesia,id',
            'id_contrato' => 'nullable|exists:contratos,id',
            'tipo_egreso' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
            'responsable' => 'required|string|max:255',
            'comprobante' => 'nullable|string|max:255',
        ];
    }
}
