<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class IngredientAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ingredients = Ingredient::get();
        return response()->json($ingredients);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        return response()->json($ingredient);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Ingredient::class);

            $ingredient = Ingredient::create($request->json()->all());
            return response()->json($ingredient, 201);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id); // Retrieve the ingredient
            $this->authorize('update', $ingredient);

            $ingredient->update($request->json()->all());
            return response()->json($ingredient);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id);
            $ingredient->delete();
            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }

    /**
     * Approve the specified resource .
     */
    public function approve(string $id)
    {
        try {
            $ingredient = Ingredient::findOrFail($id); // Retrieve the ingredient
            $this->authorize('update', $ingredient);

            // Perform your logic to approve the ingredient, for example, update the "approved" flag to true
            $ingredient->status = 1;
            $ingredient->save();

            return response()->json(['message' => 'Ingredient approved successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }
}
