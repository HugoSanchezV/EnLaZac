<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailSetting\UpdateMailSettingRequest;
use App\Models\MailSetting;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Redirect;

class MailSettingController extends Controller
{
    public function edit()
    {
        $settings = MailSetting::all()->first();

        return Inertia::render('Admin/Settings/Email/Edit', [
            'settings' => $settings,
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
        ]);
    }

    public function update(UpdateMailSettingRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $account = MailSetting::first();
            if (!$account) {
                $account = MailSetting::create([
                    'transport' =>$validatedData['transport'],
                    'host' =>$validatedData['host'],
                    'port' =>$validatedData['port'],
                    'encryption' =>$validatedData['encryption'],
                    'username' =>$validatedData['username'],
                    'password' =>$validatedData['password'],
                    'address' =>$validatedData['address'],
                    'name' =>$validatedData['name'],
                ]);
            } else {
                $account->update($request->
                only(['transport',
                'host',
                'port',
                'encryption',
                'username',
                'password',
                'address',
                'name']));
                }

            return Redirect::route('settings.email.edit')->with('success', 'Email Actualizado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('settings.email.edit')->with('error', 'Hubo un error al actualizar las credenciales');
        }
    }
}
