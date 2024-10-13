<?php

namespace App\Imports;

use App\Models\Router;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RouterImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Router([
            //
            'ip_address' => $row['direccion'],
            'user' => $row['usuario'],
            'password' => Crypt::encrypt($row['password']),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'direccion' => 'required|ip|unique:routers,ip_address',
            'usuario' => 'required|string|max:25', // Cambia a string con un tamaño máximo
            'password' => 'required|string|min:8', // Cambia las reglas según tus necesidades
        ];
    }

    public function messages()
    {
        return [
            'direccion.required' => 'La dirección IP es obligatoria.',
            'direccion.ip' => 'La dirección IP debe ser válida.',
            'direccion.unique' => 'Esta dirección IP ya está en uso.',
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'usuario.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'usuario.max' => 'El nombre de usuario no puede tener más de 25 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
