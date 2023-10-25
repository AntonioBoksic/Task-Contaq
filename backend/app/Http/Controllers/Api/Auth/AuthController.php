<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// l'assenza di questo non mi dava errore durante login anche se crea un cookie ma mi dava problemi durante logout, da capire perchè
use Illuminate\Support\Facades\Cookie;


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

        return response()->json(['success' => true, 'message' => 'User registered successfully!'], 201);
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

        $userData = auth()->user();

        return response()->json(['success' => true,'token' => $token, 'user' => $userData, 'message' => 'Logged in successfully!'])
        ->withCookie(cookie('auth_token', $token, 60, null, null, false, true));  // 60 minuti di validità
        // per capire a cosa si riferiscono gli argomenti di cookie()
        // cookie($name = null, $value = null, $minutes = null, $path = null, $domain = null, $secure = null, $httpOnly = null, $raw = false, $sameSite = null)

    }

    public function logout(Request $request) {

        if (!auth()->check()) {
            return response()->json(['message' => 'Token not valid'], 401);
        }

        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully!'])
                ->withCookie(Cookie::forget('auth_token'));
    }
}

