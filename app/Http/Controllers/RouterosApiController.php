<?php

namespace App\Http\Controllers;

use App\Models\RouterosAPI;
use Inertia\Inertia;

class RouterosApiController extends Controller
{
    //
    public function index()
    {
        $ip = "201.131.239.27";
        $user = "hugosv";
        $password = "CWsm=G9^";

        $API = new RouterosAPI();

        $data = [];

        $API->debug(false);
        if ($API->connect($ip, $user, $password)) {
            $identitas = $API->comm('/user/print');

            dd($identitas);
            $data = [
                'identitas' => $identitas[0],
            ];
        } else {
            return 'No se logro la conexion';
        }

        return Inertia::render('TestApi', compact($data));
    }
}
