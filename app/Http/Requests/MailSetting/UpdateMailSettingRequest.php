<?php

namespace App\Http\Requests\MailSetting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMailSettingRequest extends FormRequest
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
            'transport' => 'required|string|in:smtp,sendmail,mailgun,ses',
            'host' => 'required|string',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|string|in:ssl,tls',
            'username' => 'required|string',
            'password' => 'required|string',
            'address' => 'required|email',
            'name' => 'required|string|max:255',
        ];
    }
    public function messages()
    {
        return [
            'transport.required' => 'El campo de transporte es obligatorio.',
            'transport.string' => 'El campo de transporte debe ser una cadena de texto.',
            'transport.in' => 'El transporte debe ser uno de los siguientes valores: smtp, sendmail, mailgun, ses.',

            'host.required' => 'El campo de host es obligatorio.',
            'host.string' => 'El campo de host debe ser una cadena de texto.',

            'port.required' => 'El campo de puerto es obligatorio.',
            'port.integer' => 'El campo de puerto debe ser un número entero.',
            'port.min' => 'El campo de puerto debe ser al menos 1.',
            'port.max' => 'El campo de puerto no puede ser mayor a 65535.',

            'encryption.string' => 'El campo de encriptación debe ser una cadena de texto.',
            'encryption.in' => 'La encriptación debe ser ssl o tls.',

            'username.required' => 'El campo de nombre de usuario es obligatorio.',
            'username.string' => 'El campo de nombre de usuario debe ser una cadena de texto.',

            'password.required' => 'El campo de contraseña es obligatorio.',
            'password.string' => 'El campo de contraseña debe ser una cadena de texto.',

            'address.required' => 'El campo de dirección de correo es obligatorio.',
            'address.email' => 'El campo de dirección de correo debe ser una dirección de correo válida.',

            'name.required' => 'El campo de nombre es obligatorio.',
            'name.string' => 'El campo de nombre debe ser una cadena de texto.',
            'name.max' => 'El campo de nombre no puede tener más de 255 caracteres.',
        ];
    }
}
