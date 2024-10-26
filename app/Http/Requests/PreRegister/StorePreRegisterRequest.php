<?php

namespace App\Http\Requests\PreRegister;

use DragonCode\Contracts\Cashier\Auth\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StorePreRegisterRequest extends FormRequest
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
            'phone' => [
                'required',
                'string',
                'size:10',
                'unique:pre_register_users,phone',
                'unique:users,phone',
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
