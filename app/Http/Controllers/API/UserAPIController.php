<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['profile']);
    }

    public function profile(Request $request)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Auth Token Error'], 401);
        }

        return response()->json(['email' => $user->email, 'is_admin' => $user->is_admin]);
    }
}
