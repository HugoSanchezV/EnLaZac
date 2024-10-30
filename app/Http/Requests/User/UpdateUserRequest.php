<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'required',
                'string',
                'size:10', // Exactamente 12 caracteres
                // 'unique:pre_register_users,phone,' . $userId, 
                Rule::unique('users', 'phone')->ignore($userId),
                //'unique:users,phone', // Verificación en users (para evitar duplicados)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'admin' => 'required|integer|in:0,2,3',
        ];
    }
}
