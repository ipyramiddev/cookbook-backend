<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Cart;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartApiControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_a_recipe_to_the_cart()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $recipe = Recipe::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/cart/add/' . $recipe->id);

        $response->assertOk();
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function it_can_remove_a_recipe_from_the_cart()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $recipe = Recipe::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'quantity' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/cart/remove/' . $recipe->id);

        $response->assertOk();
        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    /** @test */
    public function it_can_retrieve_the_cart_items()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $recipe1 = Recipe::factory()->create();
        $recipe2 = Recipe::factory()->create();

        $cart1 = Cart::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe1->id,
            'quantity' => 1,
        ]);

        // Uncomment if you want to test with multiple items in the cart
        // $cart2 = Cart::factory()->create([
        //     'user_id' => $user->id,
        //     'recipe_id' => $recipe2->id,
        //     'quantity' => 2,
        // ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/cart');

        $response->assertOk();
        $response->assertJson([
            [
                'id' => $recipe1->id,
                'title' => $recipe1->title,
            ],
            // Uncomment if you want to test with multiple items in the cart
            // [
            //     'id' => $recipe2->id,
            //     'title' => $recipe2->title,
            // ],
        ]);
    }

    // Add more test cases for other methods like it_can_add_a_recipe_to_the_cart, it_can_remove_a_recipe_from_the_cart, etc.
}
