<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// l'assenza di questo non mi dava errore durante login anche se crea un cookie ma mi dava problemi durante logout, da capire perchè
use Illuminate\Support\Facades\Cookie;

// questo serve per le date, a me per fare controllo validità token
use Carbon\Carbon;


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

        \Log::info('Token generato al login:', [$token]);
        
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

    public function verifyToken(Request $request) {
        \Log::info('Inizio verifica del token.');
    
        $authHeader = $request->header('Authorization');
        $tokenFromHeader = str_replace('Bearer ', '', $authHeader);
        \Log::info('Token inviato nell\'header:', [$tokenFromHeader]);
    
        if (!auth()->check()) {
            \Log::info('L\'utente non è autenticato.');
            return response()->json(['success' => false, 'message' => 'Token not valid'], 401);
        }
    
        // Ottieni tutti i token dell'utente autenticato
        $tokens = auth()->user()->tokens;
        \Log::info('Token associati all\'utente autenticato:', $tokens->pluck('id', 'name')->toArray());
    
        $tokenMatch = false;
        foreach ($tokens as $dbToken) {
            \Log::info('Confronto del token hashato inviato con il token hashato nel database:', [hash('sha256', $tokenFromHeader), $dbToken->token]);
            if (hash_equals(hash('sha256', $tokenFromHeader), $dbToken->token)) {
                $tokenMatch = true;
                break;
            }
        }
    
        if (!$tokenMatch) {
            \Log::info('Token non trovato tra quelli dell\'utente.');
            return response()->json(['success' => false, 'message' => 'Token not found in user tokens'], 401);
        }
    
        // controlla la validità del token basata sulla sua data di creazione e validità
        $tokenCreationDate = Carbon::parse($dbToken->created_at);
        \Log::info('Data di creazione del token:', [$tokenCreationDate]);
    
        if ($tokenCreationDate->diffInMinutes(Carbon::now()) > 60) {
            \Log::info('Token scaduto.');
            // Token scaduto
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        }
    
        // Ottieni l'utente associato al token
        $user = auth()->user();
        \Log::info('Utente associato al token:', [$user->id, $user->name]);
    
        return response()->json(['success' => true, 'user' => $user, 'token' => $dbToken->token]);
    }
    
    
    
    
}

