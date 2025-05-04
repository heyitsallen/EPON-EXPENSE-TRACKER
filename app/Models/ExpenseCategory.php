<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['tracker_id', 'name'];

    // Define the relationship with Tracker
    public function tracker()
    {
        return $this->belongsTo(Tracker::class);
    }

    // Define the relationship with Expense
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
