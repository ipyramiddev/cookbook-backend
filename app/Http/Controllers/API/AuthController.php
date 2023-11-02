<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->json()->all();

            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials'],
                ]);
            }

            $user = Auth::user();

            return response()->json(['token' => $token, 'is_admin' => $user->is_admin]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
