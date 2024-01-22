<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Rating;
use Illuminate\Support\Facades\Validator;

class RecipeRatingController extends Controller
{

    public function rateRecipe(Request $request, $recipeId)
    {
        // Validate the request data
        $validator = Validator::make($request->all(),[
            'rating' => 'required|numeric|between:1,5',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // Find the authenticated user 
        $user = auth()->user();

        // Find the recipe
        $recipe = Recipe::findOrFail($recipeId);

        // Check if the user has already rated the recipe
        $existingRating = Rating::where('recipe_id', $recipe->id)
            ->where('rated_by', $user->id)
            ->first();

        if ($existingRating) {
            // User has already rated, update the existing rating or throw an error
            $existingRating->update(['rating' => $request->input('rating')]);
        } else {
            // Create a new rating
            Rating::create([
                'recipe_id' => $recipe->id,
                'rated_by' => $user->id,
                'rating' => $request->input('rating'),
            ]);
        }

        // Recalculate and update the average rating of the recipe
        $recipe->averageRating();

        return response()->json(['message' => 'Recipe rated successfully']);
    }
}
