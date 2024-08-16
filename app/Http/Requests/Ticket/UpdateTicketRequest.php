<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the ticket is authorized to make this request.
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
            'subject' => 'required|max:255',
            'description' => 'required|max:255',
            'status' => 'required|string|in:"0","1","2","3"'
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'El asunto es un campo obligatorio.',
            'subject.max' => 'La asunto no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es un campo obligatorio',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'status.required' => 'El estado es un campo obligatorio.',
            'status.string' => 'El estado debe ser una cadena.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
