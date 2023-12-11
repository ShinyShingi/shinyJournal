<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookApiController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider, and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/profile/{username}', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
   // Route::post('/profile', [BookController::class, 'store']);
    // Other protected routes...

});

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'authenticatedUser']);


Route::get('{username}/books', [\App\Http\Controllers\UserController::class, 'getUserBooks']);

Route::get('books', [BookApiController::class, 'index']);
Route::post('books', [BookController::class, 'store']);
Route::put('books/{id}', [BookController::class, 'updateBook']);
Route::post('books/{id}', [BookController::class, 'updateBook']);
