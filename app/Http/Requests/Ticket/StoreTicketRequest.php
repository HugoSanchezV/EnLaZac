<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'ubication' => 'nullable|string|max:255',
            'create_date' => 'required|string|email|max:255|unique:users,email',
            'status' => 'required|string|min:8|confirmed',
            'user_id' => 'required|integer|in:0,2,3',
        ];
    }
}
