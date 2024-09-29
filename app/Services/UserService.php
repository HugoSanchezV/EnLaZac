<?php

namespace App\Services;

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
}
