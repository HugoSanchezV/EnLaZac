<?php

namespace App\Http\Requests\Installation;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstallationRequest extends FormRequest
{
    /**
     * Determine if the change is authorized to make this request.
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
            'contract_id' => ['required','exists:contracts,id'],
            'description' => ['required', 'in:"1","2"'],
            'assigned_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages()
    {
        return [
            'contract_id.required' => 'El id del contrato es un campo obligatorio.',
            'contract_id.exists' => 'El contrato no existe.',
            'description.required' => 'La descripción es un campo obligatorio.',
            'description.in' => 'La descripción ingresada no es admitida',
            'assigned_date.required' => 'La fecha es un campo obligatorio.',
            'assigned_date.date' => 'La fecha debe ser tipo date.',
            'assigned_date.after_or_equal' => 'La fecha asignada debe ser igual o mayor a la fecha actual.', // Mensaje para after_or_equal'assigned_date.required' => 'La fecha es un campo obligatorio.',
        ];
    }
}
