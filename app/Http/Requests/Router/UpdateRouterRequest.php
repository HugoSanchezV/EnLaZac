<?php

namespace App\Http\Requests\Router;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRouterRequest extends FormRequest
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
        $routerId = $this->route('id');
        return [
            'ip_address' => [
                'required',
                'ip',
                Rule::unique('routers', 'ip_address')->ignore($routerId)
            ],
            'user' => 'required|string|max:25',
            'password' => 'sometimes|nullable|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'ip_address.required' => 'La dirección IP es obligatoria.',
            'ip_address.ip' => 'La dirección IP debe ser válida.',
            'ip_address.unique' => 'Esta dirección IP ya está en uso.',
            'user.required' => 'El nombre de usuario es obligatorio.',
            'user.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'user.max' => 'El nombre de usuario no puede tener más de 25 caracteres.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
