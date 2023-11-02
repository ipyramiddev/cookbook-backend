<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Recipe;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Exception;

class CartAPIController extends Controller
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

        $cart = Cart::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($cart) {
            $cart->quantity += 1;
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'quantity' => 1,
            ]);
        }

        return response()->json(['cart' => $cart]);
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

        $cart = Cart::where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cart->delete();

        return response()->json(['message' => 'Cart item deleted successfully']);
    }

    public function getCart(Request $request)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid User'], 404);
        }

        $userId = $user->id; // Retrieve the authenticated user's ID

        // $recipes = Recipe::whereHas('carts', function ($query) use ($userId) {
        //     $query->where('user_id', $userId);
        // })->get();

        // return response()->json($recipes);

        // $cartItems = Cart::where('user_id', $userId)->select('id', 'quantity', 'recipe_id')->with('recipe.ingredients')->get();
        $cartItems = Cart::where('user_id', $userId)
            ->select('id', 'quantity', 'recipe_id')
            ->with(['recipe' => function ($query) {
                $query->with(['ingredients' => function ($query) {
                    $query->select('ingredients.id', 'ingredients.name', 'ingredients.unit', 'ingredients.price', 'recipe_ingredients.amount');
                }]);
            }])
            ->get();
        return response()->json($cartItems);
    }
}
