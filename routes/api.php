<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\RecipeAPIController;
use App\Http\Controllers\API\IngredientAPIController;
use App\Http\Controllers\API\FeedbackAPIController;
use App\Http\Controllers\API\FavoriteAPIController;
use App\Http\Controllers\API\CartAPIController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth API endpoints
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
});

// User Profile endpoint
Route::get('/profile', [UserAPIController::class, 'profile']);

// Recipe API endpoints
Route::get('/recipes', [RecipeAPIController::class, 'index']);
Route::get('/recipes/{id}', [RecipeAPIController::class, 'show']);
Route::post('/recipes', [RecipeAPIController::class, 'store']);
Route::put('/recipes/{id}', [RecipeAPIController::class, 'update']);
Route::delete('/recipes/{id}', [RecipeAPIController::class, 'destroy']);
Route::post('/recipes/{id}/approve', [RecipeAPIController::class, 'approve']);

// Ingredient API endpoints
Route::get('/ingredients', [IngredientAPIController::class, 'index']);
Route::get('/ingredients/{id}', [IngredientAPIController::class, 'show']);
Route::post('/ingredients', [IngredientAPIController::class, 'store']);
Route::put('/ingredients/{id}', [IngredientAPIController::class, 'update']);
Route::delete('/ingredients/{id}', [IngredientAPIController::class, 'destroy']);

// Recipe Feedback API endpoints
Route::get('/recipes/{recipeId}/feedback', [FeedbackAPIController::class, 'getFeedbacksForRecipe']);
Route::post('/recipes/{recipeId}/feedback', [FeedbackAPIController::class, 'store']);
Route::delete('/recipes/{recipeId}/feedback/{id}', [FeedbackAPIController::class, 'destroy']);

// Recipe Cart API endpoints
Route::post('/recipes/{id}/cart_add', [CartAPIController::class, 'add']);
Route::post('/recipes/{id}/cart_remove', [CartAPIController::class, 'remove']);
Route::get('/cart', [CartAPIController::class, 'getCart']);

// Recipe Favorite API endpoints
Route::post('/recipes/{id}/favorite_add', [FavoriteAPIController::class, 'add']);
Route::post('/recipes/{id}/favorite_remove', [FavoriteAPIController::class, 'remove']);
Route::get('/favorite', [FavoriteAPIController::class, 'getFavourites']);

