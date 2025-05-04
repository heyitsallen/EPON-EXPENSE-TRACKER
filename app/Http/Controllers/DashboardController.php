<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Tracker;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\RecurringBill;
use App\Models\RecurringIncome;
use App\Models\SavingGoal;
use App\Models\Income;

class DashboardController extends Controller
{
    public function show(Tracker $tracker)
    {
        $owner = $tracker->users()->wherePivot('position', 'owner')->first();
        $expenses = $tracker->expenses()->get()->groupBy('date');
        
        // Get all the new data for the dashboard
        $recurringIncome = $tracker->recurringIncomes;
        $savingGoals = $tracker->savingGoals;
        $expenseCategories = $tracker->expenseCategories;
        $recurringBills = $tracker->recurringBills;
        $monthlyBudget = $tracker->monthlyBudget;  // You need to have this field or a method to retrieve it

        return view('dashboard', compact(
            'tracker', 'owner', 'expenses', 
            'recurringIncome', 'savingGoals', 'expenseCategories', 'recurringBills', 'monthlyBudget'
        ));
    }
    
}
