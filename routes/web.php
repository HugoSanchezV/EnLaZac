<?php

use App\Http\Controllers\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('DashboardBase', [
            'user' => Auth::user(),
        ]);
    })->name('dashboard');

    Route::get('/usuarios', [User::class, 'index'])->name('usuarios');

    Route::get('/usuarios/create', [User::class, 'create'])->name('usuarios.create');
});
