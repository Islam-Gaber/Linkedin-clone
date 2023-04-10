<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExperienceRequest;
use App\Models\Experience;
use App\Models\Profile;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function store(ExperienceRequest $request, $profile_uuid)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $experience = new Experience($request->validated());
        $experience->profile_id = $profile->id;
        $experience->save();

        return response()->json([
            'experience' => $experience,
        ], 201);
    }

    public function update(ExperienceRequest $request, $profile_uuid, $experience_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $experience = Experience::where('profile_id', $profile->id)->where('id', $experience_id)->first();

        if (!$experience) {
            return response()->json(['error' => 'Experience not found.'], 404);
        }

        $experience->update($request->validated());

        return response()->json([
            'experience' => $experience,
        ]);
    }

    public function destroy($profile_uuid, $experience_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $experience = Experience::where('profile_id', $profile->id)->where('id', $experience_id)->first();

        if (!$experience) {
            return response()->json(['error' => 'Experience not found.'], 404);
        }

        $experience->delete();

        return response()->json([
            'message' => 'Experience deleted successfully.',
        ]);
    }
}
