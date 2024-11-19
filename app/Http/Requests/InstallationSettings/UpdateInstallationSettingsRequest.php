<?php

namespace App\Http\Requests\InstallationSettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstallationSettingsRequest extends FormRequest
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
            'exemption_months' => ['nullable', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'exemption_months.numeric' => 'El o los meses deben ser valores numericos',
        ];
    }
}
