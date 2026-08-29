<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MinisterioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'liderable_id' => 'nullable|integer',
            'liderable_type' => 'nullable|string|in:App\Models\Miembro,App\Models\LiderIglesia',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('liderable_type');
            $id = $this->input('liderable_id');

            if ($type && $id) {
                $tabla = $type === 'App\Models\Miembro' ? 'miembros' : 'lideres_iglesia';
                if (!\DB::table($tabla)->where('id', $id)->exists()) {
                    $validator->errors()->add('liderable_id', 'El líder seleccionado no existe.');
                }
            }
        });
    }
}
