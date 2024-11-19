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
            'inv_device_id' => 'required|exists:inventorie_devices,id',
            'plan_id'=> 'required|exists:plans,id',
            'start_date' => 'required|date_format:Y-m', // Fecha de inicio en formato Y-m
            'end_date' => 'required|date_format:Y-m|after:start_date', // Fecha de fin en formato Y-m y después de start_date
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
            'inv_device_id.required' => 'El id del dispositivo es un campo obligatorio.',
            'inv_device_id.exist' => 'El id del dispositivo debe existir.',
            'plan_id.required' => 'El id del plan de internet es un campo obligatorio.',
            'plan_id.exist' => 'El id del plan de internet debe existir.',
            'start_date.date_format' => 'La fecha de inicio debe tener el formato Año-Mes (Y-m).',
            'end_date.date_format' => 'La fecha de finalización debe tener el formato Año-Mes (Y-m).',
            'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
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
