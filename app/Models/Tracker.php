<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tracker extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tracker_user')
                    ->withPivot('position')
                    ->withTimestamps();
    }

    public function owner()
    {
        return $this->users()->wherePivot('position', 'owner');
    }

    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class);
    }
    
    public function incomes()
    {
        return $this->hasMany(\App\Models\Income::class);
    }
    

    public function recurringIncomes()
{
    return $this->hasMany(RecurringIncome::class);
}

public function savingGoals()
{
    return $this->hasMany(SavingGoal::class);
}

public function expenseCategories()
{
    return $this->hasMany(ExpenseCategory::class);
}

public function recurringBills()
{
    return $this->hasMany(RecurringBill::class);
}
    
}