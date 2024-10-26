<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Events\RegisterUserEvent;
use App\Models\PreRegisterUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    private $messages = [
        'phone.required' => 'El número de teléfono es obligatorio.',
        'phone.size' => 'El número de teléfono debe tener exactamente 10 dígitos.',
        'phone.unique' => 'Este número de teléfono ya está registrado.',
        'phone.exists' => 'Este número de teléfono no se encuentra en el pre-registro, contacta con tu proovedor.',
    ];

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $user = DB::transaction(function () use ($input) {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'size:10', 'unique:pre_register_users,phone', 'exists:pre_register_users,phone', 'unique:users,phone',],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            ], $this->messages)->validate();

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => Hash::make($input['password']),
            ]);

            self::deletePreRegister($input['phone']);

            self::make_register_notification($user);

            return $user;
        });

        return $user;
    }
    static function make_register_notification($user)
    {
        event(new RegisterUserEvent($user));
    }

    public function deletePreRegister($phone)
    {
        $register = PreRegisterUser::where('phone', $phone)->first();

        if ($register) {
            $register->delete();
        }
    }
}
