<?php

namespace App\Http\Requests\InventorieDevice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventorieDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'mac_address' => 'required|unique:inventorie_devices,mac_address|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', // Dirección MAC válida
            'description' => 'required|string|max:255', // Descripción requerida
            'brand'       => 'required|string|max:100', // Marca requerida
        ];
    }

    public function messages()
    {
        return [
            'mac_address.required' => 'La dirección MAC es obligatoria.',
            'mac_address.unique'   => 'La dirección MAC ya existe en el inventario.',
            'mac_address.regex'    => 'El formato de la dirección MAC no es válido.',
            'status.required'      => 'El estado es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max'      => 'La descripción no puede tener más de 255 caracteres.',
            'brand.required'       => 'La marca es obligatoria.',
            'brand.max'            => 'La marca no puede tener más de 100 caracteres.',
        ];
    }
}
