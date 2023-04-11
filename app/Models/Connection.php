<?php

namespace App\Models;

use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connection extends Model
{
    use Uuids;
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_REQUESTED = 'requested';

    protected $fillable = [
        'user_id',
        'connection_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function connection()
    {
        return $this->belongsTo(User::class, 'connection_id', 'id');
    }
}
