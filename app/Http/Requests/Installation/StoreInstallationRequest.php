<?php

namespace App\Http\Requests\Installation;

use App\Models\Contract;
use App\Models\Installation;
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
            'contract_id' => ['required', 'exists:contracts,id'],
            'description' => ['required', 'in:1,2'],
            'assigned_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_id.required' => 'El id del contrato es un campo obligatorio.',
            'contract_id.exists' => 'El contrato no existe.',
            'description.required' => 'La descripción es un campo obligatorio.',
            'description.in' => 'La descripción ingresada no es admitida.',
            'assigned_date.required' => 'La fecha es un campo obligatorio.',
            'assigned_date.date' => 'La fecha debe ser tipo date.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar si el contrato asociado existe
            $contract = Contract::find($this->contract_id);

            if ($contract) {
                // Validar que assigned_date no sea inferior a start_date
                $assignedDate = \Carbon\Carbon::parse($this->assigned_date);
                $startDate = \Carbon\Carbon::parse($contract->start_date);

                if ($assignedDate->lt($startDate)) {
                    $validator->errors()->add('assigned_date', 'La fecha asignada no puede ser inferior a la fecha de inicio del contrato.');
                }
            }

            // Validación adicional para descripción "1"
            if ($this->description == '1') {
                $installationExists = Installation::where('contract_id', $this->contract_id)
                    ->where('description', '1')
                    ->exists();

                if ($installationExists) {
                    $validator->errors()->add('description', 'El contrato ya tiene una instalación inicial');
                }
            }
        });
    }
}
