<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketStatusRequest extends FormRequest
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
      
        return [
            'status' => 'required|string|in:"0","1","2","3"',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'El estado es un campo obligatorio.',
            'status.string' => 'El estado debe ser una cadena.',
            'status.in' => 'El estado seleccionado no es válido.',
           
        ];
    }
}
