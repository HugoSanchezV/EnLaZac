<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    //
    public function index()
    {
        // dd('Estas en la configuracion del sistema');

        return Inertia::render('Admin/Settings/Index');
    }
}
