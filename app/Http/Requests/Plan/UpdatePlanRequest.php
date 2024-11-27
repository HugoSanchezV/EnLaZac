<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdatePlanRequest extends FormRequest
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
            'name' => 'sometimes|required|max:100',
            'description' => 'sometimes|required|max:255',
            'price' => 'sometimes|required|numeric',

            'burst_limit.upload_limits' => 'sometimes|required|string',
            'burst_limit.download_limits' => 'sometimes|required|string',

            'burst_threshold.upload_limits' => 'sometimes|required|string',
            'burst_threshold.download_limits' => 'sometimes|required|string',

            'burst_time.upload_limits' => 'sometimes|required|string',
            'burst_time.download_limits' => 'sometimes|required|string',

            'limite_at.upload_limits' => 'sometimes|required|string',
            'limite_at.download_limits' => 'sometimes|required|string',

            'max_limit.upload_limits' => 'sometimes|required|string',
            'max_limit.download_limits' => 'sometimes|required|string',
        ];
    }

    /**
     * Custom validation logic for nested fields.
     */
    protected function passedValidation()
    {
        $data = $this->validated();

        $uploadLimits = [
            'burst_limit' => isset($data['burst_limit']['upload_limits']) ? $this->convertToKbps($data['burst_limit']['upload_limits']) : null,
            'burst_threshold' => isset($data['burst_threshold']['upload_limits']) ? $this->convertToKbps($data['burst_threshold']['upload_limits']) : null,
            'burst_time' => isset($data['burst_time']['upload_limits']) ? $this->convertToSeconds($data['burst_time']['upload_limits']) : null,
            'limit_at' => isset($data['limite_at']['upload_limits']) ? $this->convertToKbps($data['limite_at']['upload_limits']) : null,
            'max_limit' => isset($data['max_limit']['upload_limits']) ? $this->convertToKbps($data['max_limit']['upload_limits']) : null,
        ];

        $downloadLimits = [
            'burst_limit' => isset($data['burst_limit']['download_limits']) ? $this->convertToKbps($data['burst_limit']['download_limits']) : null,
            'burst_threshold' => isset($data['burst_threshold']['download_limits']) ? $this->convertToKbps($data['burst_threshold']['download_limits']) : null,
            'burst_time' => isset($data['burst_time']['download_limits']) ? $this->convertToSeconds($data['burst_time']['download_limits']) : null,
            'limit_at' => isset($data['limite_at']['download_limits']) ? $this->convertToKbps($data['limite_at']['download_limits']) : null,
            'max_limit' => isset($data['max_limit']['download_limits']) ? $this->convertToKbps($data['max_limit']['download_limits']) : null,
        ];

        if (!empty(array_filter($uploadLimits))) {
            $this->validateMikroTikLimits($uploadLimits, 'upload_limits');
        }

        if (!empty(array_filter($downloadLimits))) {
            $this->validateMikroTikLimits($downloadLimits, 'download_limits');
        }
    }

    private function validateMikroTikLimits(array $limits, string $type)
    {
        if (isset($limits['limit_at'], $limits['max_limit']) && $limits['limit_at'] > $limits['max_limit']) {
            throw ValidationException::withMessages([
                "limit_at.$type" => "El limit_at debe ser menor o igual al max_limit en $type.",
            ]);
        }

        if (isset($limits['max_limit'], $limits['burst_threshold']) && $limits['max_limit'] > $limits['burst_threshold']) {
            throw ValidationException::withMessages([
                "max_limit.$type" => "El max_limit debe ser menor o igual al burst_threshold en $type.",
            ]);
        }

        if (isset($limits['burst_threshold'], $limits['burst_limit']) && $limits['burst_threshold'] > $limits['burst_limit']) {
            throw ValidationException::withMessages([
                "burst_threshold.$type" => "El burst_threshold debe ser menor o igual al burst_limit en $type.",
            ]);
        }

        if (isset($limits['burst_time']) && $limits['burst_time'] <= 0) {
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
            'name.sometimes' => 'El nombre es opcional, pero debe ser un texto válido.',
            'name.max' => 'El nombre no puede tener más de 100 caracteres.',
            'description.sometimes' => 'La descripción es opcional, pero debe ser un texto válido.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'price.sometimes' => 'El precio es opcional, pero debe ser un número válido.',
            'burst_limit.upload_limits.sometimes' => 'El burst limit de subida es opcional, pero debe ser válido.',
            'burst_limit.download_limits.sometimes' => 'El burst limit de bajada es opcional, pero debe ser válido.',
            'burst_threshold.upload_limits.sometimes' => 'El burst threshold de subida es opcional, pero debe ser válido.',
            'burst_threshold.download_limits.sometimes' => 'El burst threshold de bajada es opcional, pero debe ser válido.',
            'burst_time.upload_limits.sometimes' => 'El burst time de subida es opcional, pero debe ser válido.',
            'burst_time.download_limits.sometimes' => 'El burst time de bajada es opcional, pero debe ser válido.',
            'limite_at.upload_limits.sometimes' => 'El límite en subida es opcional, pero debe ser válido.',
            'limite_at.download_limits.sometimes' => 'El límite en bajada es opcional, pero debe ser válido.',
            'max_limit.upload_limits.sometimes' => 'El max limit de subida es opcional, pero debe ser válido.',
            'max_limit.download_limits.sometimes' => 'El max limit de bajada es opcional, pero debe ser válido.',
        ];
    }
}
