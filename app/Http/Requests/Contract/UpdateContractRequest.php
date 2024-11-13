<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
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
            'device' => 'required|exists:devices,id',
            'plan_id'=> 'required|exists:plans,id',
            'start_date'=> 'required',
            'end_date'=> 'required',
            'active'=> 'required',
            'address'=> 'required|max:100',
            'rural_community_id'=> 'required|exists:rural_communities,id',
            'geolocation.latitude'=> 'required|numeric',
            'geolocation.longitude'=> 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'device.required' => 'El id del dispositivo es un campo obligatorio.',
            'device.exist' => 'El id del dispositivo debe existir.',
            'plan_id.required' => 'El id del plan de internet es un campo obligatorio.',
            'plan_id.exist' => 'El id del plan de internet debe existir.',
            'start_date.required' => 'La fecha de inicio es un campo obligatorio.',
            'end_date.required' => 'La fecha de terminación es un campo obligatorio.',
            'address.required' => 'La direccion es un campo obligatorio.',
            'active.required' => 'El estado del contrato es un campo obligatorio.',
            'address.max' => 'La dirección no puede tener más de 100 caracteres.',
            'rural_community_id.required' => 'El id de la comunidad es un campo obligatorio.',
            'rural_community_id.exist' => 'El id de la comunidad debe existir.',
            'geolocation.latitude.numeric' => 'La latitud debe ser de tipo numérico',
            'geolocation.latitude.numeric' => 'La longitud debe ser de tipo numérico',
            'geolocation.latitude.required' => 'La latitud es un campo obligatorio.',
            'geolocation.latitude.required' => 'La longitud es un campo obligatorio.',
        ];
    }
}
