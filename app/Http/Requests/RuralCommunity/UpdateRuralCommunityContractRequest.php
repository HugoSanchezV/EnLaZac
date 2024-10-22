<?php

namespace App\Http\Requests\RuralCommunity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuralCommunityContractRequest extends FormRequest
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
            'contract_id' => 'nullable|exists:contracts,id',
        ];
    }

    public function messages()
    {
        return [
            'contract_id.exists' => 'Debe existir contrato',
         ];
    }
}
