<?php

namespace App\Imports;

use App\Models\User;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Convertir el número a cadena y limpiar caracteres no numéricos
        // $phone = preg_replace('/\D/', '', (string)$row['numero']);

        // // Validar que sea numérico y de 10 dígitos
        // if (!is_numeric($phone) || strlen($phone) !== 10) {
        //     throw new \Exception("El número de teléfono debe ser numérico y tener exactamente 10 dígitos. Dato recibido: {$phone}");
        // }

        $user = new User([
            'name' => $row['nombre'],
            'email' => $row['email'],
            'alias' => $row['alias'],
            'phone' => strval($row['numero']),
            'password' => Hash::make($row['password']),
            'admin' => $row['role'] === 1 ? 0 : $row['role'],
        ]);

        $user->save();

        UserTelegramService::createContactTelegramSendMessage([
            "name" => $user->name,
            "alias" => $user->alias,
            "phone" => '52' . $user->phone,
        ], new TelegramService());

        return $user;
    }

    /**
     * Define las reglas de validación que se aplicarán a cada fila.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'numero' => 'required|size:10|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => 'required|integer|in:0,2,3',
        ];
    }
}
