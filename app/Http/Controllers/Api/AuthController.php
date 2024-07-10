<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data Saved',
            'data' => []
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if(!empty($user)){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken('myToken')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Login Successfull',
                    'data' => ['user' => $user, 'token' => $token]
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'Wrong Password',
                    'data' => []
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => []
            ]);
        }
        return $request;
    }

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'status' => true,
            'message' => 'Profile Data',
            'data' => [$user]
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Log Out',
            'data' => []
        ]);
    }
}
