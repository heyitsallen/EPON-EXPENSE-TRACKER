<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringIncome extends Model
{
    protected $fillable = ['tracker_id', 'description', 'amount', 'interval'];

    public function tracker()
    {
        return $this->belongsTo(Tracker::class);
    }
}
