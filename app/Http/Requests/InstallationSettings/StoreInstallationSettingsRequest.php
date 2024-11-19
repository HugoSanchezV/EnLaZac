<?php

namespace App\Http\Requests\InstallationSettings;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstallationSettingsRequest extends FormRequest
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
            'installation_id' => ['required','exists:installations,id'],
            'exemption_months' => ['nullable', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'installation_id.required' => 'El id la instalación es un campo obligatorio.',
            'installation_id.exists' => 'La instalación no existe.',
            'exemption_months.numeric' => 'El o los meses deben ser valores numericos',
        ];
    }
}
