<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillRequest;
use App\Models\Profile;
use App\Models\ProfileSkill;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use apiResponseTrait;
    public function store(SkillRequest $request, $profile_uuid)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $skill = Skill::create($request->validated());

        $profileSkill = new ProfileSkill();
        $profileSkill->profile_id = $profile->id;
        $profileSkill->skill_id = $skill->id;
        $profileSkill->endorsement_count = 0;
        $profileSkill->save();

        return $this->apiResponse('skill created', ['profile_skill' => $profileSkill], ($skill), [], 201);
        /* return response()->json([
            'skill' => $skill,
            'profile_skill' => $profileSkill,
        ], 201); */
    }

    public function update(SkillRequest $request, $profile_uuid, $skill_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $skill = Skill::find($skill_id);

        if (!$skill) {
            return response()->json(['error' => 'Skill not found.'], 404);
        }

        $skill->update($request->validated());

        return $this->apiResponse('skill updated', [], ($skill), [], 201);
        /* return response()->json([
            'skill' => $skill,
        ], 200); */
    }

    public function destroy($profile_uuid, $skill_id)
    {
        $profile = Profile::where('uuid', $profile_uuid)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $skill = Skill::find($skill_id);

        if (!$skill) {
            return response()->json(['error' => 'Skill not found.'], 404);
        }

        $skill->delete();

        return $this->apiResponse('Skill deleted successfully.', [], [], [], 201);
        /* return response()->json([], 204); */
    }
}
