<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'subject' => 'required|max:255',
            'description' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'El asunto es un campo obligatorio.',
            'subject.max' => 'La asunto no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es un campo obligatorio',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
        ];
    }
}
