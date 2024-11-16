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
        $user = new User([
            'name' => $row['nombre'],
            'email' => $row['email'],
            'alias' => $row['alias'],
            'phone' => $row['numero'],
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
            'phone' => 'required|string|size:10|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => 'required|integer|in:0,2,3',
        ];
    }
}
