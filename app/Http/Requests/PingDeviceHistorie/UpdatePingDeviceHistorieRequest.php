<?php

namespace App\Http\Requests\PingDeviceHistorie;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePingDeviceHistorieRequest extends FormRequest
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
            'device_id' => ['required', 'exists:devices,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['string', 'max:255'],
        ];
    }
}
