<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
            'price' => 'required|numeric',

            'burst_limit.upload_limits' => 'required|string',
            'burst_limit.download_limits' => 'required|string',

            'burst_threshold.upload_limits' => 'required|string',
            'burst_threshold.download_limits' => 'required|string',

            'burst_time.upload_limits' => 'required|string',
            'burst_time.download_limits' => 'required|string',

            'limite_at.upload_limits' => 'required|string',
            'limite_at.download_limits' => 'required|string',

            'max_limit.upload_limits' => 'required|string',
            'max_limit.download_limits' => 'required|string',
        ];
    }

    /**
     * Custom validation logic for nested fields.
     */
    protected function passedValidation()
    {
        $data = $this->validated();

        $uploadLimits = [
            'burst_limit' => $this->convertToKbps($data['burst_limit']['upload_limits']),
            'burst_threshold' => $this->convertToKbps($data['burst_threshold']['upload_limits']),
            'burst_time' => $this->convertToSeconds($data['burst_time']['upload_limits']),
            'limit_at' => $this->convertToKbps($data['limite_at']['upload_limits']),
            'max_limit' => $this->convertToKbps($data['max_limit']['upload_limits']),
        ];

        $downloadLimits = [
            'burst_limit' => $this->convertToKbps($data['burst_limit']['download_limits']),
            'burst_threshold' => $this->convertToKbps($data['burst_threshold']['download_limits']),
            'burst_time' => $this->convertToSeconds($data['burst_time']['download_limits']),
            'limit_at' => $this->convertToKbps($data['limite_at']['download_limits']),
            'max_limit' => $this->convertToKbps($data['max_limit']['download_limits']),
        ];

        $this->validateMikroTikLimits($uploadLimits, 'upload_limits');
        $this->validateMikroTikLimits($downloadLimits, 'download_limits');
    }

    private function validateMikroTikLimits(array $limits, string $type)
    {
        // MikroTik rules validation
        if ($limits['limit_at'] > $limits['max_limit']) {
            throw ValidationException::withMessages([
                "limit_at.$type" => "El limit_at debe ser menor o igual al max_limit en $type.",
            ]);
        }

        if ($limits['max_limit'] > $limits['burst_threshold']) {
            throw ValidationException::withMessages([
                "max_limit.$type" => "El max_limit debe ser menor o igual al burst_threshold en $type.",
            ]);
        }

        if ($limits['burst_threshold'] > $limits['burst_limit']) {
            throw ValidationException::withMessages([
                "burst_threshold.$type" => "El burst_threshold debe ser menor o igual al burst_limit en $type.",
            ]);
        }

        if ($limits['burst_time'] <= 0) {
            throw ValidationException::withMessages([
                "burst_time.$type" => "El burst_time debe ser mayor a 0 en $type.",
            ]);
        }
    }

    private function convertToKbps(string $value): float
    {
        preg_match('/^(\d+(\.\d+)?)([kKmM]?)$/', $value, $matches);

        if (!$matches) {
            throw ValidationException::withMessages([
                'invalid_format' => "El valor '$value' tiene un formato inválido. Debe incluir un número seguido de 'k' o 'M'.",
            ]);
        }

        $number = (float)$matches[1];
        $unit = strtolower($matches[3]);

        return match ($unit) {
            'm' => $number * 1024, // Convertir Mbps a Kbps
            'k' => $number, // Mantener Kbps
            default => $number // Sin unidad, asumir Kbps
        };
    }

    private function convertToSeconds(string $value): int
    {
        preg_match('/^(\d+)(s?)$/', $value, $matches);

        if (!$matches) {
            throw ValidationException::withMessages([
                'invalid_format' => "El valor '$value' tiene un formato inválido. Debe ser un número seguido de 's' para segundos.",
            ]);
        }

        return (int)$matches[1];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es un campo obligatorio.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'description.required' => 'La descripción es un campo obligatorio.',
            'price.required' => 'El precio es un campo obligatorio.',
            'price.numeric' => 'El campo :attribute debe ser un número.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'burst_limit.upload_limits.required' => 'El burst limit de subida es obligatorio.',
            'burst_limit.download_limits.required' => 'El burst limit de bajada es obligatorio.',
            'burst_threshold.upload_limits.required' => 'El burst threshold de subida es obligatorio.',
            'burst_threshold.download_limits.required' => 'El burst threshold de bajada es obligatorio.',
            'burst_time.upload_limits.required' => 'El burst time de subida es obligatorio.',
            'burst_time.download_limits.required' => 'El burst time de bajada es obligatorio.',
            'limite_at.upload_limits.required' => 'El límite en subida es obligatorio.',
            'limite_at.download_limits.required' => 'El límite en bajada es obligatorio.',
            'max_limit.upload_limits.required' => 'El max limit de subida es obligatorio.',
            'max_limit.download_limits.required' => 'El max limit de bajada es obligatorio.',
        ];
    }
}
