<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    protected $fillable = ['tracker_id', 'name', 'target_amount', 'progress'];

    public function tracker()
    {
        return $this->belongsTo(Tracker::class);
    }
}
