<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Get the user object
            $user = Auth::user();

            // Update user online status
            $user->is_online = true;
            $user->save();

            // Generate access token
            $token = $user->createToken('api_token')->plainTextToken;

            // Return user data
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_img' => $user->profile_img,
                'created_at' => $user->created_at,
                'is_online' => $user->is_online,
                'uuid' => $user->uuid,
            ];

            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $userData,
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    }

    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create user
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->is_online = true;
            $user->save();

            // Commit transaction
            DB::commit();

            // Generate access token
            $token = $user->createToken('api_token')->plainTextToken;

            // Return user data
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_img' => $user->profile_img,
                'created_at' => $user->created_at,
                'is_online' => $user->is_online,
            ];

            return response()->json([
                'message' => 'User created successfully',
                'user' => $userData,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Roll back transaction if an exception occurs
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->is_online = false;
        $user->save();

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function forgotPassword(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find user by email address
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No user found with that email address',
            ], 404);
        }

        // Generate new password
        $password = Str::random(8);
        $user->password = Hash::make($password);
        $user->save();

        // Send email with new password
        Mail::to($user->email)->send(new ResetPassword($password));

        return response()->json([
            'message' => 'Password reset successfully. Check your email for the new password.',
        ]);
    }
}
