<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Events\RegisterUserEvent;
use App\Jobs\RegisterTelegramContactJob;
use App\Models\PreRegisterUser;
use App\Models\TelegramAccount;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    private $messages = [
        'phone.required' => 'El número de teléfono es obligatorio.',
        'phone.min' => 'El número de teléfono debe comenzar con 52 (México) o 1 (EE.UU.) y tener exactamente 10 dígitos después',
        'phone.max' => 'El número de teléfono debe comenzar con 52 (México) o 1 (EE.UU.) y tener exactamente 10 dígitos después',
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
        Log::info('Entre al servicio');
        $user = DB::transaction(function () use ($input) {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'min:11', 'max:12',  'exists:pre_register_users,phone', 'unique:users,phone',],
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

        self::registerContact($user);
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

    public function registerContact($user)
    {
        RegisterTelegramContactJob::dispatch($user);
    }
}
