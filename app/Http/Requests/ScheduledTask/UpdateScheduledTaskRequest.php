<?php

namespace App\Http\Requests\ScheduledTask;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduledTaskRequest extends FormRequest
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
            'name' => 'required|in: "ping-routers","device-stats","check-contracts", "backups"',
            'period' => 'required|in: "everyFiveMinutes","everyFifteenMinutes","everyThirtyMinutes","hourly", "daily", "weekly", "monthly"' ,
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'period.in' => 'El periodo ingresado no esta permitido',
            'period.required' => 'El periodo es un campo obligatorio',
            'status.required' => 'El estado de la tarea es una campo obligatorio',
            'status.boolean' => 'El estado de la tarea debe ser de tipo booleano',
         ];
    }
}
