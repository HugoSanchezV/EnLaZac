<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'name' => 'required|max:100',
            'description' => 'required|max:255',

            'burst_limit.upload_limits' => 'required|numeric',
            'burst_limit.download_limits' => 'required|numeric',


            'burst_threshold.upload_limits' => 'required|numeric',
            'burst_threshold.download_limits' => 'required|numeric',

            'burst_time.upload_limits' => 'required|numeric',
            'burst_time.download_limits' => 'required|numeric',

            'limite_at.upload_limits' => 'required|numeric',
            'limite_at.download_limits' => 'required|numeric',

            'max_limit.upload_limits' => 'required|numeric',
            'max_limit.download_limits' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es un campo obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'description.required' => 'La descripción es un campo obligatorio.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'burst_limit.upload_limits.required' => 'El burst limit de subida es obligatorio.',
            'burst_threshold.upload_limits.required' => 'El burst threshold de subida es obligatorio.',
            'burst_time.upload_limits.required' => 'El burst time de subida es obligatorio.',
            'limite_at.upload_limits.required' => 'El límite en subida es obligatorio.',
            'max_limit.upload_limits.required' => 'El max limit de subida es obligatorio.',

            'burst_limit.download_limits.required' => 'El burst limit de bajada es obligatorio.',
            'burst_threshold.download_limits.required' => 'El burst threshold de bajada es obligatorio.',
            'burst_time.download_limits.required' => 'El burst time de bajada es obligatorio.',
            'limite_at.download_limits.required' => 'El límite en bajada es obligatorio.',
            'max_limit.download_limits.required' => 'El max limit de bajada es obligatorio.',

            'burst_limit.*.numeric' => 'El campo :attribute debe ser un número.',
            'burst_threshold.*.numeric' => 'El campo :attribute debe ser un número.',
            'burst_time.*.numeric' => 'El campo :attribute debe ser un número.',
            'limite_at.*.numeric' => 'El campo :attribute debe ser un número.',
            'max_limit.*.numeric' => 'El campo :attribute debe ser un número.',
        ];
    }
}
