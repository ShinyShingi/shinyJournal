<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;


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
//require __DIR__.'/auth.php';

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



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get("profile/books", [BookController::class, 'index'])->middleware('auth');


Route::get('create', [BookController::class, 'create']);
Route::post('store-data', [BookController::class,'store']);
Route::post('/updateStatus/{id}', [BookController::class, 'updateStatus']);
Route::delete('/updateBook/{id}', [BookController::class,'destroy']);

Route::get('/getBook/{id}', [BookController::class,'getBook']);





Route::get('/{any}', function () {
    return view('home');
})->where('any', '.*');

