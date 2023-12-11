<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use  App\Models\User;

class UserController extends Controller
{
    public function authenticatedUser(Request $request)
    {
        return response()->json($request->user());
    }

    public function getUser($username) {

        return response()->json($request->user());



//        $user = User::where('username', $username)->first();


//        if (!$user) {
//            return response()->json(['message' => 'User not found'], 404);
//        }
//
//        return response()->json($user);
    }

    public function getUserBooks($username)
    {
        Log::info("Requested username: " . $username);

        $user = User::with('books')->where('username', $username)->first();

        if (!$user) {
            Log::info("User not found for username: " . $username);
            return response()->json(['message' => 'User not found'], 404);
        }

        Log::info("Books retrieved for user: " . $username);
        return response()->json(['books' => $user->books]);
    }



}
