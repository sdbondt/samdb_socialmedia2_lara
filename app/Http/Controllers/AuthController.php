<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signup() {
        $attr = request()->validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:7', 'max:255', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/']
        ]);

        $user = User::create($attr);
        $token = $user->createToken('authToken')->plainTextToken;

        return ['token' => $token];
    }

    public function login() {
        $attr = request()->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);
        $user = User::where('email', request('email'))->first();
        if(!$user) {
            return ['msg' => 'Invalid credentials'];
        } elseif(!auth()->attempt($attr)) {
            return ['msg' => 'Invalid credentials'];
        } else {
            $token = $user->createToken('authToken')->plainTextToken;
            return ['token' => $token];
        }
    }

    public function logout() {
        request()->user()->currentAccessToken()->delete();
        return ['msg' => 'Logged out.'];
    }
}
