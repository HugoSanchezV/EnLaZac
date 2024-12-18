<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function getTypeUser($type)
    {

        if ($type === 0) {
            return 'usuario';
        }

        if ($type === 2) {
            return 'coordinador';
        }

        if ($type === 3) {
            return 'técnico';
        }
    }

    public static function needUpdateUser(User $user, $validatedData)
    {
        $currentPhone = $user->phone;

        $newPhone = $validatedData['phone'] ?? $user->phone;
        
        $currentFirstName = $user->name;
        $newFirstName = $validatedData['first_name'] ?? $user->first_name;

        $currentLastName = $user->phone;
        $newLastName = $validatedData['last_name'] ?? $user->last_name;

        if ($currentPhone !== $newPhone || $currentFirstName !== $newFirstName || $currentLastName !== $newLastName) {
            return true;
        }

        return false;
    }
}
