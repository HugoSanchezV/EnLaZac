<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PayController extends Controller
{
    //
    public function index() {
        return Inertia::render('User/Pays/Pays');
    }
}
