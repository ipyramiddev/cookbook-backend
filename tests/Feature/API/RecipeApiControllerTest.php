<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class RecipeApiControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_retrieve_all_recipes_with_ingredients()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Recipe::factory()->count(3)->create();
        Ingredient::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/recipes');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'instructions',
                    'ingredients' => [
                        '*' => [
                            'id',
                            'name',
                            'pivot' => [
                                'recipe_id',
                                'ingredient_id',
                                'amount',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_retrieve_a_specific_recipe_with_ingredients()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $recipe = Recipe::factory()->create();
        $ingredients = Ingredient::factory()->count(2)->create();
        $recipe->ingredients()->attach($ingredients);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/recipes/' . $recipe->id);

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'instructions',
            'ingredients' => [
                '*' => [
                    'id',
                    'name',
                    'pivot' => [
                        'recipe_id',
                        'ingredient_id',
                        'amount',
                    ],
                ],
            ],
        ]);
    }
}
