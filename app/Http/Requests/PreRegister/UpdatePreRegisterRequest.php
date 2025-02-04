<?php

namespace App\Http\Requests\PreRegister;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreRegisterRequest extends FormRequest
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
        $preRegisterUserId = $this->route('pre_register_user'); // Suponiendo que pasas el ID del pre-registro en la ruta
        return [
            'phone' => [
                'required',
                'string',
                'min:11',
                'max:12',
                'unique:pre_register_users,phone,' . $preRegisterUserId, // Ignora el registro actual en pre_register_users
                'unique:users,phone', // Verificación en users (para evitar duplicados)
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.unique' => 'El número de teléfono ya está registrado.',
            'phone.size' => 'El número de teléfono debe tener exactamente 12 dígitos.',
        ];
    }
}
