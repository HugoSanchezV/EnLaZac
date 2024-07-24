<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class User extends Controller
{
    //
    public function index() {
        return Inertia::render('Admin/Users/Usuarios', [
            'user' => Auth::user(),
        ]);
    }

    public function create() {
        return Inertia::render('Admin/Users/Create', [
            'user' => Auth::user(),
        ]);
    }
}
