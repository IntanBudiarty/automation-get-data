<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutomationController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Web Frontend Controller Routes
Route::get('/dashboard', [AutomationController::class, 'index'])->name('dashboard');
Route::post('/automation/start', [AutomationController::class, 'start'])->name('automation.start');
