<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecipeImages;
use Illuminate\Support\Facades\Storage;
use App\Models\Recipe;

class RecipeImageController extends Controller
{
    public function uploadImage(Request $request, $recipeId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ensure the associated recipe exists
        $recipe = Recipe::findOrFail($recipeId);

        // Create a new RecipeImages instance
        $recipeImage = new RecipeImages();
        $recipeImage->recipe_id = $recipeId;

        // Upload the image
        $image = $request->file('image');
        $imagePath = $image->store('recipe_images', 'public');
        $recipeImage->image_path = Storage::url($imagePath);

        // Save the recipe image
        $recipeImage->save();

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'image' => $recipeImage,
        ]);
    }

}
