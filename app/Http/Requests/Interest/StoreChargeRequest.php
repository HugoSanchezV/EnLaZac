<?php

namespace App\Http\Requests\Charge;

use Illuminate\Foundation\Http\FormRequest;

class StoreChargeRequest extends FormRequest
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
            'contract_id' => 'required|exists:contracts,id',
            'description' => 'required|max:255',

            'amount' => 'required|numeric',
            'paid' => 'required|boolean',
            'date_paid' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'contract_id.required' => 'El id del contrato es un campo obligatorio.',
            'contract_id.exists' => 'Debe existir contrato',
            'description.required' => 'La descripción es un campo obligatorio.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'amount.required' => 'El monto es un campo obligatorio',
            'amount.numeric' => 'El monto debe ser de tipo numérico',
            'paid.required' => 'La confimarción del pago es un campo obligatorio',
            'paid.boolean' => 'El confirmación debe ser de tipo booleano',
        ];
    }
}
