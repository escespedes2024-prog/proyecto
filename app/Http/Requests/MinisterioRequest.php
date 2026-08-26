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
}
