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

class RecipeAPIController extends Controller
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
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
        }

        $query = Recipe::with('ingredients');
        // Search Filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        $status = 1;
        if ($user && $user->isAdmin()) {
            if ($request->has('status')) {
                $status = $request->input('status');
            }
        }

        if ($status >= 0) {
            $query->where('status', '=', $status);
        }

        // Pagination
        $perPage = $request->input('per_page', 10); // Number of results per page
        $recipes = $query->paginate($perPage);

        // $recipes = Recipe::paginate(10);
        return response()->json($recipes);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recipe = Recipe::with(['ingredients' => function ($query) {
            $query->select('ingredients.id', 'ingredients.name', 'ingredients.unit', 'ingredients.price', 'recipe_ingredients.amount');
        }])->findOrFail($id);
        return response()->json($recipe);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', Recipe::class);

            $user = null;
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (JWTException $e) {
            }

            // Validation rules
            $rules = [
                'title' => 'required',
                'instructions' => 'required',
                'ingredients' => 'required|array',
                'ingredients.*.id' => 'required|exists:ingredients,id',
                'ingredients.*.amount' => 'required|numeric|min:0',
            ];

            $data = $request->json()->all();

            // Create a new Validator instance
            $validator = Validator::make($data, $rules);

            // Validate the request data
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $insert_data = [
                'title' => $data['title'],
                'description' => $data['description'],
                'instructions' => $data['instructions'],
                'user_id' => $user['id']
            ];

            $recipe = Recipe::create($insert_data);

            $ingredients = $data['ingredients'];
            foreach ($ingredients as $ingredient) {
                $recipe->ingredients()->attach($ingredient['id'], ['amount' => $ingredient['amount']]);
            }

            return response()->json($recipe, 201);
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
            $recipe = Recipe::findOrFail($id); // Retrieve the recipe
            $this->authorize('update', $recipe);

            $recipe->update($request->json()->all());
            return response()->json($recipe);
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
            $recipe = Recipe::findOrFail($id);
            $recipe->delete();
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
            $recipe = Recipe::findOrFail($id); // Retrieve the recipe
            $this->authorize('update', $recipe);

            // Perform your logic to approve the recipe, for example, update the "approved" flag to true
            $recipe->status = 1;
            $recipe->save();

            return response()->json(['message' => 'Recipe approved successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }
}
