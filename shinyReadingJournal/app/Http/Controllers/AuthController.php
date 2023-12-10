<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    use HasApiTokens;

    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'username' => 'required|max:40',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validatedData->fails()) {
            return response(['errors'=>$validatedData->errors()], 422);
        }


        $user = User::create([
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        $token = $user->createToken('Token Name')->plainTextToken;

        return response(['user' => $user, 'token' => $token, 'message' => 'User successfully registered'], 201);
    }



    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // $user = User::where('email', $credentials['email'])->first();

        // if ($user && Hash::check($credentials['password'], $user->password)) {
        //     $token = $user->createToken('Token Name')->plainTextToken;
        //     return response()->json(['token' => $token], 200);
        // }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $token = $user->createToken('Token Name')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);

    }

    public function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate();     
        $request->session()->regenerateToken();
        // if (Auth::check()) {
        //     $request->session()->regenerate();
        //     $user = Auth::user();
        //     $token = $user->createToken('Token Name')->plainTextToken;
        //     return response()->json([
        //         'token' => $token,
        //         'user' => $user
        //     ], 200);
        // }

        return response()->json(['success' => 'Logged Out']);

    }

}
