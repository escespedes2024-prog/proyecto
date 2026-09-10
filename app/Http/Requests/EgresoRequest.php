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
            'id_contrato' => 'nullable|exists:contratos,id',
            'tipo_egreso' => 'required|string|in:Mantenimiento,Servicios Básicos,Salarios / Honorarios,Ayuda Comunitaria,Otros Egresos',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
            'responsable' => 'required|string|exists:lideres_iglesia,nombre',
            'comprobante' => 'nullable|string|max:255',
        ];
    }
}
