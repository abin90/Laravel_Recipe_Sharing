<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Activity;
use App\Models\Tags;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    //function to view user's recipes
    public function getUserRecipes(Request $request,$userId)
    {
       
        $recipes = Recipe::where('user_id', $userId)->get();

        return response()->json($recipes);
    }

    //function to create a recipe
    public function createRecipe(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'ingredients' => 'required|string',
        'steps' => 'required|string',
        'cooking_time' => 'required|integer',
        'difficulty_level' => 'required|in:easy,medium,difficult',
        'tags' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = $request->user();

    // Create the recipe
    $recipe = $user->recipes()->create([
        'title' => $request->input('title'),
        'ingredients' => $request->input('ingredients'),
        'steps' => $request->input('steps'),
        'cooking_time' => $request->input('cooking_time'),
        'difficulty_level' => $request->input('difficulty_level'),
    ]);

    $activity = new Activity([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
        'added_date' => now(),
    ]);

    $tags = explode(',', $request->input('tags'));
    foreach ($tags as $tagName) {
        $tag = new Tags([
            'tag_name' => trim($tagName),
            'recipe_id' => $recipe->id,
        ]);
        $tag->save();
    }

    $activity->save();

    return response()->json(['message' => 'Recipe created successfully', 'recipe' => $recipe], 201);
}

    public function viewRecipe(Request $request, $recipeId)
{
        try {
            // Find the recipe by ID
            $recipe = Recipe::findOrFail($recipeId);

            // Load related data (e.g., user, images, likes, ratings, tags, activity)
            //$recipe->load('user', 'images', 'recipeLikes', 'ratings', 'tag', 'activity');

            return response()->json([
                'success' => true,
                'data' => $recipe,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe not found.',
            ], 404);
        }
}

    //function to update a recipe
    public function updateRecipe(Request $request, $recipeId)
    {
       
        $validator = Validator::make($request->all(),[
            'title' => 'string',
            'ingredients' => 'string',
            'steps' => 'string',
            'cooking_time' => 'integer',
            'difficulty_level' => 'in:easy,medium,difficult',
            'rating' => 'numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
       
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['error' => 'Recipe not found'], 404);
        }

     
        $recipe->update($request->all());

      
        return response()->json(['message' => 'Recipe updated successfully', 'recipe' => $recipe]);
    }

    // function for deleting a recipe
    public function deleteRecipe(Request $request, $recipeId)
    {
        
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['error' => 'Recipe not found'], 404);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }

    //function for liking a recipe
    public function likeRecipe(Request $request,$recipeId)
    {
        
        $recipe = Recipe::findOrFail($recipeId);
        //return response()->json($recipe->recipeLikes);
        // Check if the user has already liked the recipe
        if ($recipe->recipeLikes()->where('liked_by', Auth::id())->count()==0) {
            $recipe->recipeLikes()->attach(Auth::user());
            $recipe->updateLikes(); // Update likes count in the Recipe model
            return response()->json(['message' => 'Recipe liked']);
        }

        return response()->json(['message' => 'Recipe already liked']);
    }

    // function for unlike a recipe
    public function unlikeRecipe($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);

        // Check if the user has liked the recipe
        if ($recipe->recipeLikes()->where('liked_by', Auth::id())->exists()) {
            $recipe->recipeLikes()->detach(Auth::id());
            $recipe->updateLikes(false); // Update likes count in the Recipe model
            return response()->json(['message'=>'Recipe unliked']);
        }

        return response()->json(['message'=>'You are not liked the recipe. Please like recipe first']);
    }

    //function for activity feed
    public function recipesByFollowingUsers(Request $request)
{
    if (auth()->check()) {
        $user = auth()->user();
        $perPage = $request->input('limit', 3);
        
        // Get recipes added by users whom the authenticated user is following
        $followIds = $user->followers()->pluck('follow_id');
        $recipes = Recipe::whereIn('user_id', $followIds)
            ->with('user') // Eager load the user relationship
            ->paginate($perPage);

        $data = [
            'recipes' => $recipes->items(), 
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total(),
            ],
        ];

        return response()->json($data, 200);
    }

    // If the user is not authenticated, return an unauthorized response
    return response()->json(['message' => 'Unauthorized'], 401);
}


}
