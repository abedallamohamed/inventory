<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login and return API token
     */
    public function login(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required_without:username|email',
            'username' => 'required_without:email|email', 
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $identifier = $request->email ?? $request->username;
        $user = User::where('email', $identifier)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Authentication failed',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ], 401);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user information
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}