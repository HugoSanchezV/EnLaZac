<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|size:10|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'admin' => 'required|integer|in:0,2,3',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'El número de teléfono ya está registrado.',
        ];
    }
}
