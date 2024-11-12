<?php

namespace App\Http\Requests\Interest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterestRequest extends FormRequest
{
    /**
     * Determine if the change is authorized to make this request.
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
            'amountCourt' => 'required|numeric',
            'amountDebt' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'amountCourt.required' => 'El monto es un campo obligatorio',
            'amountCourt.numeric' => 'El monto debe ser de tipo numérico',
            'amountDebt.required' => 'El monto es un campo obligatorio',
            'amountDebt.numeric' => 'El monto debe ser de tipo numérico',
         ];
    }
}
