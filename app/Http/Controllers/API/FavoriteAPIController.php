<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserFavorite;
use App\Models\Recipe;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Exception;

class FavoriteAPIController extends Controller
{
    public function add(Request $request, string $id)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid User'], 404);
        }

        $userId = $user->id;
        $recipeId = $id;

        try {
            $recipe = Recipe::findOrFail($id); // Retrieve the recipe
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid Recipe'], 404);
        }

        $favorite = UserFavorite::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($favorite) {
        } else {
            $favorite = UserFavorite::create([
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'quantity' => 1,
            ]);
        }

        return response()->json(['favorite' => $favorite]);
    }

    public function remove(Request $request, string $id)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid User'], 404);
        }

        $userId = $user->id;
        $recipeId = $id;
        try {
            $recipe = Recipe::findOrFail($id); // Retrieve the recipe
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid Recipe'], 404);
        }

        $favorite = UserFavorite::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'UserFavorite item not found'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'UserFavorite item deleted successfully']);
    }

    public function getFavourites(Request $request)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid User'], 404);
        }

        $userId = $user->id; // Retrieve the authenticated user's ID

        $recipes = Recipe::whereHas('favorites', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
        return response()->json($recipes);

        // return response()->json($recipes);

        // $favoriteItems = UserFavorite::where('user_id', $userId)->with('recipe')->get();
        // return response()->json($favoriteItems);
    }
}
