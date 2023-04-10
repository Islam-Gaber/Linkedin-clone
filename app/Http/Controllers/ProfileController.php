<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::with(['user', 'experiences', 'educations', 'skills'])
            ->get();

        $profiles = $profiles->filter(function ($profile) {
            return Gate::allows('view', $profile);
        });

        if ($profiles->isEmpty()) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json([
            'profiles' => $profiles,
        ]);
    }

    public function show($uuid)
    {
        $profile = Profile::where('uuid', $uuid)
            ->with([
                'user:id,name,email,profile_img,is_online',
                'experiences:id,profile_id,title,company,location,start_date,end_date,description',
                'educations:id,profile_id,school,degree,field_of_study,start_date,end_date,description',
                'skills:id,name'
            ])
            ->firstOrFail(['id', 'uuid', 'user_id', 'headline', 'summary', 'country', 'state', 'city', 'address', 'phone_number', 'website_url']);

        return response()->json([
            'profile' => $profile,
        ]);
    }

    public function store(ProfileRequest $request)
    {
        $user_id = auth()->user()->id; // get the authenticated user's id
        $profile = new Profile($request->validated());
        $profile->user_id = $user_id;
        $profile->save();

        return response()->json([
            'profile' => $profile,
        ], 201);
    }

    public function update(ProfileRequest $request, $uuid)
    {
        // Find the profile by uuid
        $profile = Profile::where('uuid', $uuid)->first();

        // Check if the profile exists
        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        // Validate that only authenticated user can update their own profile
        if ($profile->user_id != Auth::id()) {
            return response()->json(['error' => 'Forbidden.'], 403);
        }

        // Update profile data
        $profile->update($request->validated());

        return response()->json([
            'profile' => $profile,
        ]);
    }
}
