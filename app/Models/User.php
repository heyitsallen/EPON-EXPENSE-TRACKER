<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Trackers the user owns (created).
     */
    public function ownedTrackers()
    {
        return $this->hasMany(Tracker::class, 'user_id');
    }

    /**
     * Trackers shared with the user.
     */
    public function sharedTrackers()
    {
        return $this->belongsToMany(Tracker::class, 'tracker_user')
                    ->withPivot('position')
                    ->withTimestamps()
                    ->wherePivot('position', '!=', 'owner');
    }

    /**
     * Get all trackers: owned + shared (optional).
     */
    public function allTrackers()
    {
        return $this->ownedTrackers->merge($this->sharedTrackers);
    }
}
