<?php

namespace App\Http\Requests\Email;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailRequest extends FormRequest
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
            'name' => 'required|max:50',
            'email' => [
                'required',
                'email',
                'max:255',
            ]
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre es un campo obligatorio.',
            'name.max' => 'El campo nombre no debe pasar los 255 caracteres',
            'email.requierd' => 'El campo email es obligatorio',
            'email.email' => 'El campo email debe tener formato de email',
            'email.max' => 'El campo nombre no debe pasar los 255 caracteres',
            
        ];
    }
}
