<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Tracker;
use App\Models\User;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{

    public function showDashboard($trackerId)
    {
        $tracker = Tracker::with(['expenses', 'incomes'])->findOrFail($trackerId);
    
        $owner = $tracker->users()->wherePivot('position', 'owner')->first();
    
        // Grouped Income and Expenses by date (Y-m-d)
        $incomesGrouped = $tracker->incomes()->orderBy('date')->get()->groupBy(function($item) {
            return Carbon::parse($item->date)->format('Y-m-d');
        });
    
        $expensesGrouped = $tracker->expenses()->orderBy('date')->get()->groupBy(function($item) {
            return Carbon::parse($item->date)->format('Y-m-d');
        });
    
        // Monthly/Yearly Totals
        $totalMonthlyIncome = $tracker->incomes()->whereMonth('date', now()->month)->sum('amount');
        $totalYearlyIncome = $tracker->incomes()->whereYear('date', now()->year)->sum('amount');
    
        $totalMonthlyExpenses = $tracker->expenses()->whereMonth('date', now()->month)->sum('amount');
        $totalYearlyExpenses = $tracker->expenses()->whereYear('date', now()->year)->sum('amount');
    
        // Labels: January to December
        $months = collect(range(1, 12))->map(function ($m) {
            return Carbon::create()->month($m)->format('F');
        });
    
        // Monthly Income/Expense Totals
        $monthlyIncomeData = $months->map(function ($monthName, $index) use ($tracker) {
            return $tracker->incomes()
                ->whereMonth('date', $index + 1)
                ->whereYear('date', now()->year)
                ->sum('amount');
        });
    
        $monthlyExpenseData = $months->map(function ($monthName, $index) use ($tracker) {
            return $tracker->expenses()
                ->whereMonth('date', $index + 1)
                ->whereYear('date', now()->year)
                ->sum('amount');
        });
    
        // Pie chart data: Top 5 expense descriptions
        $topDescriptions = $tracker->expenses()
            ->selectRaw('description, SUM(amount) as total')
            ->groupBy('description')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    
        return view('trackers.dashboard', compact(
            'tracker',
            'owner',
            'incomesGrouped',
            'expensesGrouped',
            'totalMonthlyIncome',
            'totalYearlyIncome',
            'totalMonthlyExpenses',
            'totalYearlyExpenses',
            'months',
            'monthlyIncomeData',
            'monthlyExpenseData',
            'topDescriptions'
        ));
    }
    public function store(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'tracker_id' => 'required|exists:trackers,id', // Ensure tracker exists
            'user_id' => 'required|exists:users,id', // Ensure user exists
            'type' => 'required|in:expense,income',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
            'new_category' => 'nullable|string|max:100', // Ensure new_category is optional
            'category_id' => 'nullable|exists:expense_categories,id', // Ensure category_id exists in DB
        ]);
    
        // Check if the user selected to create a new category
        if ($request->category_id === 'new' && $request->filled('new_category')) {
            // Create a new category and associate it with the tracker
            $newCategory = ExpenseCategory::create([
                'tracker_id' => $request->tracker_id, // Use the tracker ID from the form
                'name' => $request->new_category, // Use the new category name entered by the user
            ]);
            
            // Add the newly created category ID to the data
            $data['category_id'] = $newCategory->id;
        }
    
        // Check if an attachment was uploaded
        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }
    
        // If the type is income, create an income record
        if ($data['type'] === 'income') {
            Income::create([
                'tracker_id' => $data['tracker_id'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'date' => $data['date'],
            ]);
        } else {
            // Otherwise, create an expense record
            Expense::create($data);
        }
    
        return back()->with('success', 'Transaction added!');
    }
    
    

    // Manage expenses and incomes for a specific tracker
    public function manage($trackerId)
    {
        $tracker = Tracker::findOrFail($trackerId);

        $expenses = $tracker->expenses()->latest()->get();
        $income = $tracker->incomes()->latest()->get();

        $categories = ExpenseCategory::where('tracker_id', $trackerId)->get();

        return view('expenses.manage', compact('tracker', 'expenses', 'income', 'categories'));
    }

    // Update an existing expense or income
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'type' => 'required|in:expense,income',
            'category_id' => 'nullable|exists:expense_categories,id', // Only for expenses
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
        ]);

        if ($data['type'] === 'income') {
            $income = Income::findOrFail($id);
            $income->description = $data['description'];
            $income->amount = $data['amount'];
            $income->date = $data['date'];
            $income->save();
        } else {
            $expense = Expense::findOrFail($id);
            $expense->description = $data['description'];
            $expense->amount = $data['amount'];
            $expense->date = $data['date'];
            if ($request->hasFile('attachment')) {
                $expense->attachment_path = $request->file('attachment')->store('attachments', 'public');
            }
            if (isset($data['category_id'])) {
                $expense->category_id = $data['category_id'];
            }
            $expense->save();
        }

        return back()->with('success', 'Transaction updated!');
    }

    // Delete an expense or income
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type');

        if ($type === 'income') {
            $income = Income::findOrFail($id);
            $income->delete();
        } else {
            $expense = Expense::findOrFail($id);
            $expense->delete();
        }

        return back()->with('success', 'Transaction deleted!');
    }
}
