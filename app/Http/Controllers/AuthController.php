<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
            ]
        ]);
        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password'])
        ]);
        $token = $user->createToken("API Token")->plainTextToken;
        return response()->json([
            'message' => 'Utilisateur inscrit avec succès',
            'user' => $user,
            'token' => $token
        ],201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required','string']
        ]);

        if(Auth::attempt($request->only(['email', 'password']))){
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("API Token")->plainTextToken;
            return response()->json([
                'message' => 'Utilisateur connecté avec succès',
                'user' => $user,
                'token' => $token
            ]);
        }else{
            return response()->json([
                'message' => 'L\'email et le mot de passe ne correspondent pas à notre enregistrement.',
            ], 401);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Utilisateur déconnecté avec succès'
        ]);
    }
}
