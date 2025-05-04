<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['tracker_id', 'user_id', 'description', 'amount', 'date'];

    public function tracker()
    {
        return $this->belongsTo(\App\Models\Tracker::class);
    }
}
