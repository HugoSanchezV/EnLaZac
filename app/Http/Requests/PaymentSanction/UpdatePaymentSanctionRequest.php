<?php

namespace App\Http\Requests\PaymentSanction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentSanctionRequest extends FormRequest
{
    /**
     * Determine if the contract is authorized to make this request.
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
            'status'=> 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'contract_id.required' => 'El id del contrato es un campo obligatorio.',
            'contract_id.exist' => 'El id del contrato debe existir.',
            'status.boolean' => 'El estado debe ser booleano',
        ];
    }
}
