<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|string|in:operator,technician',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Puoi anche fare l'accesso automaticamente dell'utente dopo la registrazione se lo desideri
        // $token = $user->createToken('AppName')->plainTextToken;

        return response()->json(['message' => 'User registered successfully!'], 201);
        // Se vuoi ritornare il token:
        // return response()->json(['token' => $token, 'message' => 'User registered successfully!'], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $token = auth()->user()->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token, 'message' => 'Logged in successfully!']);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully!']);
    }
}

