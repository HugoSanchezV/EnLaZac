<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeviceRequest extends FormRequest
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
            'router_id' => ['required', 'exists:routers,id'],
            'device_id' => ['nullable', 'exists:inventorie_devices,id', Rule::unique('devices', 'device_id')],
            'user_id' => ['nullable', 'exists:users,id'],
            'comment' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'ip', Rule::unique('devices', 'address')],
            'disabled' => ['boolean'],
        ];
    }
}
