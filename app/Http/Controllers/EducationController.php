<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducationRequest;
use App\Models\Education;
use App\Models\Profile;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    use apiResponseTrait;
    public function store(EducationRequest $request, $profile_uuid)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $education = new Education($request->validated());
        $education->profile_id = $profile->id;
        $education->save();

        return $this->apiResponse('education created', [], ($education), [], 201);
        /* return response()->json([
            'education' => $education,
        ], 201); */
    }

    public function update(EducationRequest $request, $profile_uuid, $education_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $education = Education::where('profile_id', $profile->id)->where('id', $education_id)->first();

        if (!$education) {
            return response()->json(['error' => 'Education not found.'], 404);
        }

        $education->update($request->validated());

        return $this->apiResponse('education updated', [], ($education), [], 201);
        /* return response()->json([
            'education' => $education,
        ]); */
    }

    public function destroy($profile_uuid, $education_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $education = Education::where('profile_id', $profile->id)->where('id', $education_id)->first();

        if (!$education) {
            return response()->json(['error' => 'Education not found.'], 404);
        }

        $education->delete();

        return $this->apiResponse('Education deleted successfully.', [], [], [], 201);
        /* return response()->json([
            'message' => 'Education deleted successfully.',
        ]); */
    }
}
