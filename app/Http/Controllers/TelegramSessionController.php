<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MadelineTrait;

class TelegramSessionController extends Controller
{

    private $madelineProto;

    public function __invoke ()
    {
        // $settings = [];
        // $settings['authorization']['default_temp_auth_key_expires_in'] = 86400*7;
        // $this->madelineProto = new \danog\MadelineProto\API('session.madeline', $setting);
        // $this->madelineProto->async(true);

        // $this->madelineProto->loop(function () {

        //     $response = yield $this->madelineProto->start();

        //     return;
        // });
    }
}