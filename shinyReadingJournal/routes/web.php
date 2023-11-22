<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('home');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/register', 'App\Http\Controllers\Auth\RegisteredUserController@create')
    ->name('register');

Route::post('/register', 'App\Http\Controllers\Auth\RegisteredUserController@store');

// Login Routes...
Route::get('/login', 'App\Http\Controllers\Auth\AuthenticatedSessionController@create')
    ->name('login');

Route::post('/login', 'App\Http\Controllers\Auth\AuthenticatedSessionController@store');

// Logout Route...
Route::post('/logout', 'App\Http\Controllers\Auth\AuthenticatedSessionController@destroy')
    ->name('logout');
