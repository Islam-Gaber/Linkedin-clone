<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ProfileSkill;
use Illuminate\Http\Request;

class ProfileSkillController extends Controller
{
    public function index()
    {
        $profileSkills = ProfileSkill::all();
        return $profileSkills;
    }
}
