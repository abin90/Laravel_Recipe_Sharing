<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeRatingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RecipeImageController;


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


// Route for registering a new user
Route::post('register',[UserController::class,'register']);

// Route for login
Route::post('login',[UserController::class,'login']);

//Route for logout
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:api');

//Route for getting user profile details
Route::get('user/profile', [UserController::class, 'getUserProfile'])->middleware('auth:api');

//Route for editing user profile details
Route::patch('/user/edit', [UserController::class, 'editUserProfile'])->middleware('auth:api');

// Route for follow a user
Route::post('/user/follow/{user}', [UserController::class, 'followUser'])->middleware('auth:api');

// Route for unfollow a user
Route::post('/user/unfollow/{user}', [UserController::class, 'unfollowUser'])->middleware('auth:api');

//Route for viewing user's recipe details
Route::get('user/{userId}/recipes', [RecipeController::class, 'getUserRecipes'])->middleware('auth:api');

//Route for creating recipe details
Route::post('/recipes/create', [RecipeController::class, 'createRecipe'])->middleware('auth:api');

//Route for viewing a single recipe
Route::get('recipe/{recipeId}', [RecipeController::class, 'viewRecipe'])->middleware('auth:api');

//Route for editing recipe details
Route::patch('/recipes/{recipeId}/edit', [RecipeController::class, 'updateRecipe'])->middleware('auth:api');

//Route for deleting  recipe details
Route::post('/recipes/{recipeId}/delete', [RecipeController::class, 'deleteRecipe'])->middleware('auth:api');

//Route for uploading  recipe images
Route::post('/recipe/{recipeId}/image', [RecipeImageController::class, 'uploadImage'])->middleware('auth:api');

//Route for rating  recipes
Route::post('/recipes/{recipeId}/rate', [RecipeRatingController::class, 'rateRecipe'])->middleware('auth:api');

//Route for liking a recipes
Route::post('/recipes/{recipeId}/like', [RecipeController::class, 'likeRecipe'])->middleware('auth:api');

//Route for unlike a recipes
Route::post('/recipes/{recipeId}/unlike', [RecipeController::class, 'unlikeRecipe'])->middleware('auth:api');

//Route for activity feeds
Route::get('/recipes/following-users', [RecipeController::class, 'recipesByFollowingUsers'])->middleware('auth:api');

//Routes for admin
Route::middleware(['admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'getUsers'])->middleware('auth:api');
    Route::get('/{userId}/recipes', [AdminController::class, 'getRecipes'])->middleware('auth:api');
    Route::post('/{recipeId}/delete', [AdminController::class, 'deleteRecipeAdmin'])->middleware('auth:api');
    Route::post('/admin/block/{user}', [AdminController::class, 'blockUser'])->middleware('auth:api');
    Route::post('/admin/unblock/{user}', [AdminController::class, 'unblockUser'])->middleware('auth:api');
    });