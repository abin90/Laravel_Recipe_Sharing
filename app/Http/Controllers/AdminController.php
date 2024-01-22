<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recipe;

class AdminController extends Controller
{
    //function to get all users
    public function getUsers(Request $request)
    {
        $users = User::all();
        return response()->json($users);
    }

    //function to get recipes by a particular user
    public function getRecipes(Request $request,$userId)
    {
       
        $recipes = Recipe::where('user_id', $userId)->get();

        return response()->json($recipes);
    }

    //function to delete a recipe
    public function deleteRecipeAdmin(Request $request, $recipeId)
    {
        
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['error' => 'Recipe not found'], 404);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }

    //function to block a user
    public function blockUser(User $user)
    {
        $user->update(['blocked' => true]);

        return response()->json(['success'=> 'User blocked successfully.']);
    }

    //function to unblock a user
    public function unblockUser(User $user)
    {
        $user->update(['blocked' => false]);

        return response()->json(['success'=> 'User unblocked.']);
    }

}
