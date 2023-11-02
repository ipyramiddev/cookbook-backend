<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Exception;

class FeedbackAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['getFeedbacksForRecipe']);
    }

    /**
     * Display a listing of the resource.
     */
    public function getFeedbacksForRecipe(Request $request, string $recipeId)
    {
        try {
            $recipe = Recipe::with('feedbacks.user')->findOrFail($recipeId);
            $feedbacks = $recipe->feedbacks;

            return response()->json(['feedbacks' => $feedbacks]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $recipeId)
    {
        try {
            $user = null;
            $user = JWTAuth::parseToken()->authenticate();

            // Validation rules
            $rules = [
                'rating' => 'required|numeric|min:0',
                'comment' => 'required',
            ];

            $data = $request->json()->all();

            // Create a new Validator instance
            $validator = Validator::make($data, $rules);

            // Validate the request data
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $insert_data = [
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'recipe_id' => $recipeId,
                'user_id' => $user['id'],
            ];

            $feedback = Feedback::create($insert_data);
            return response()->json($feedback, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $recipeId, string $id)
    {
        try {
            $user = null;
            $user = JWTAuth::parseToken()->authenticate();

            $feedback = Feedback::findOrFail($id);

            if (!$user->isAdmin() && $user->id !== $feedback->user_id) {
                return response()->json(['errors' => "Not permitted"], 403);
            }
            $feedback->delete();
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }
}
