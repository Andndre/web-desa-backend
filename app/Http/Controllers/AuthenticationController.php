<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthenticationController extends Controller
{
    public function register(Request $request) {
        try {
            $data = $request->validate([
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'min:6'],

            ]);
            $user = User::create($data);
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (Throwable) {
            return response(['message' => 'Terjadi Kesalahan'], 401);
        }
    }

    public function login(Request $request) {
        try {
            $data = $request->validate([
                'email' => ['required', 'email', 'exists:users'],
                'password' => ['required', 'min:6'],
            ]);
            $user = User::where('email', $data['email'])->first();

            if (!Hash::check($data['password'], $user->password)) {
                return response(['message' => 'Password Invalid!'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (Throwable) {
            return response(['message' => 'Terjadi Kesalahan'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
