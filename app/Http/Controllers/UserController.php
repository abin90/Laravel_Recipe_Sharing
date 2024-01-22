<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    // function for registering a new user
    public function register(Request $request){

        // validating the incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        //check for validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        //new user creation
        $data=User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'success',
            'data'=>$data
        ],200);
    }

    // function for login
    public function login(Request $request){
        $creditials=[
            'email'=>$request->email,
            'password'=>$request->password
        ];

        if(!Auth::attempt($creditials)){
            return response()->json([
                'success'=>false,
                'message'=>'email or password is incorrect'
            ],401);
        }
        $user = Auth::user();
        if ($user->blocked) {
            Auth::logout(); 
            return response()->json([
                'success' => false,
                'message' => 'User is blocked. contact admin.',
            ], 403);
        }
        if ($user->is_admin) {
            $token = $user->createToken('AdminToken')->accessToken;
        } else {
            $token = $user->createToken('UserToken')->accessToken;
        }
        return response()->json([
            'success'=>true,
            'id'=>$user->id,
            'email'=>$user->email,
            'is_admin'=>$user->is_admin,
            'token' => $token,
        ]);
    }

    //logout function
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    //function to view user profile
    public function getUserProfile(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'followers_count' => $user->followers_count,
        'following_count' => $user->following_count,
        'bio' => $user->bio,
    ]);
}
    //function to edit user profile
    public function editUserProfile(Request $request)
{
    $user = $request->user(); 

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Validating incoming request data
    $validator = Validator::make($request->all(), [
        'bio' => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user->bio = $request->bio;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User profile updated successfully',
        'data' => [
            'name' => $user->name,
            'email' => $user->email,
            'followers_count' => $user->followers_count,
            'following_count' => $user->following_count,
            'bio' => $user->bio,
        ],
    ]);
}


    public function followUser(Request $request, $userId)
    {
        $userToFollow = User::findOrFail($userId);

        // Check if the user is not already following the target user
        if (!$request->user()->following->contains($userToFollow)) {
            $request->user()->following()->attach($userToFollow);
            $request->user()->followingCount(); // Update following count
            $request->user()->followersCount();
        }

        return response()->json(['message' => 'Successfully followed user.']);
    }

    public function unfollowUser(Request $request, $userId)
    {
        $userToUnfollow = User::findOrFail($userId);

        // Check if the user is following the target user
        if ($request->user()->following->contains($userToUnfollow)) {
            $request->user()->following()->detach($userToUnfollow);
            $request->user()->followingCount(); // Update following count
            $request->user()->followersCount();
        }

        return response()->json(['message' => 'Successfully unfollowed user.']);
    }
}
