<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ClubMember;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validated = $request->validate([
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);
        $validated['password_hash'] = bcrypt($validated['password'] ?? Str::random(16));
        unset($validated['password']);
        $user = User::create($validated);
        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        return response()->json(['token' => $request->user()->createToken('api')->plainTextToken]);
    }

    public function me(Request $request) {
        return response()->json(['user' => $request->user()]);
    }
}
