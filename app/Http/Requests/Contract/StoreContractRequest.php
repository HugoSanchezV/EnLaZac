<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'user_id' => 'required',
            'plan_id'=> 'required',
            'start_date'=> 'required',
            'end_date'=> 'required',
            'active'=> 'required',
            'address'=> 'required|max:100',
            'geolocation.latitude'=> 'required|numeric',
            'geolocation.longitude'=> 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El id del usuario es un campo obligatorio.',
            'plan_id.required' => 'El id del plan de internet es un campo obligatorio.',
            'address.required' => 'La direccion es un campo obligatorio.',
            'start_date.required' => 'La fecha de inicio es un campo obligatorio.',
            'end_date.required' => 'La fecha de terminación es un campo obligatorio.',
            'active.required' => 'El estado del contrato es un campo obligatorio.',
            'address.max' => 'La dirección no puede tener más de 100 caracteres.',
        ];
    }
}
