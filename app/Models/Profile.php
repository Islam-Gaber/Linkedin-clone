<?php

namespace App\Models;

use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    use Uuids;
    /* protected $primaryKey = "uuid"; */
    /* public $incrementing = false;
    protected $keyType = 'string'; */

    protected $fillable = [
        'headline',
        'summary',
        'country',
        'state',
        'city',
        'address',
        'phone_number',
        'website_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'profile_id', 'id');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'profile_id', 'id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skills');
    }
}
