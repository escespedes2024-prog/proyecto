<?php

namespace App\Http\Requests;

use App\Models\Contrato;
use Illuminate\Foundation\Http\FormRequest;

class ContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignorarId = $this->route('contrato');
        $tipo = $this->input('tipo_compensacion');

        return [
            'id_miembro' => [
                'required',
                'exists:miembros,id',
                function ($attribute, $value, $fail) use ($ignorarId) {
                    if (Contrato::tieneSolapamiento(
                        (int) $value,
                        $this->input('fecha_inicio'),
                        $this->input('fecha_fin'),
                        $ignorarId ? (int) $ignorarId : null
                    )) {
                        $fail('El miembro ya tiene un contrato cuyas fechas se solapan con las ingresadas. Cierra o renueva el contrato anterior.');
                    }
                },
            ],
            'id_lider_iglesia' => 'required|exists:lideres_iglesia,id',
            'tipo_compensacion' => 'required|in:' . implode(',', array_keys(Contrato::tiposCompensacion())),
            'salario' => [
                'required_if:tipo_compensacion,Salario,Bono',
                'nullable',
                'numeric',
                'min:0',
            ],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ];
    }

    public function messages(): array
    {
        return [
            'salario.required_if' => 'El salario es obligatorio para contratos de tipo Salario o Bono.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ];
    }
}
