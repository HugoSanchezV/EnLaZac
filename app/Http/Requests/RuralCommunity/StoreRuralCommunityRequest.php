<?php

namespace App\Http\Requests\RuralCommunity;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuralCommunityRequest extends FormRequest
{
    /**
     * Determine if the RuralCommunity is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'installation_cost' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es un campo obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'installation_cost.required' => 'El costo es un campo obligatorio',
            'installation_cost.numeric' => 'El costo debe ser de tipo numérico',
        ];
    }
}
