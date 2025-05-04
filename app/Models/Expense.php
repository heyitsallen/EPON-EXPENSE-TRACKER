<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $fillable = ['tracker_id', 'user_id',  'category_id', 'type', 'description', 'amount', 'date', 'attachment_path'];

    public function tracker() {
        return $this->belongsTo(Tracker::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');  
    }
    
}
