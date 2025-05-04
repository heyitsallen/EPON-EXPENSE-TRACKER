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

class TrackerController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $ownedTrackers = $user->ownedTrackers()->whereNull('deleted_at')->get();
        $sharedTrackers = $user->sharedTrackers()->whereNull('deleted_at')->get();
    
        return view('trackers.index', compact('ownedTrackers', 'sharedTrackers'));
    }

    public function updateAccess(Request $request, Tracker $tracker, User $user)
{
    $this->authorizeAccess($tracker);

    $request->validate([
        'position' => 'required|in:full,partial,viewer',
    ]);

    $tracker->users()->updateExistingPivot($user->id, [
        'position' => $request->position,
    ]);

    return redirect()->back()->with('success', 'Access updated successfully.');
}

// Remove user's access
public function removeAccess(Tracker $tracker, User $user)
{
    $this->authorizeAccess($tracker);

    $tracker->users()->detach($user->id);

    return redirect()->back()->with('success', 'Access removed successfully.');
}
    public function create()
    {
        return view('trackers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:trackers,title,NULL,id,user_id,' . auth()->id(),
        ]);

        $tracker = new Tracker();
        $tracker->title = $request->title;
        $tracker->user_id = auth()->id(); // Assign user_id to the tracker
        $tracker->save();

        // Attach the user as the owner
        $tracker->users()->attach(auth()->id(), ['position' => 'owner']);

        return redirect()->route('trackers.index')->with('success', 'Tracker created successfully.');
    }

    public function edit(Tracker $tracker)
    {
        $this->authorizeAccess($tracker);
        return view('trackers.edit', compact('tracker'));
    }

    public function update(Request $request, Tracker $tracker)
    {
        $this->authorizeAccess($tracker);

        $request->validate([
            'title' => 'required|string|max:255|unique:trackers,title,' . $tracker->id,
        ]);

        $tracker->update(['title' => $request->title]);

        return redirect()->route('trackers.index')->with('success', 'Tracker updated successfully.');
    }

    public function share(Request $request, Tracker $tracker)
    {
        $this->authorizeAccess($tracker);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'position' => 'required|in:full,partial,viewer',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already shared with this tracker
        if ($tracker->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'This user is already shared with this tracker.');
        }

        // Attach user with the specified position
        $tracker->users()->attach($user->id, ['position' => $request->position]);

        return redirect()->back()->with('success', 'Tracker shared successfully.');
    }

    public function dashboard(Tracker $tracker)
    {
        if (!$tracker->users()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('trackers.index')->with('error', 'You do not have access to this tracker.');
        }

        $owner = $tracker->users()->wherePivot('position', 'owner')->first(); 
        // You can use the tracker object to fetch details related to the dashboard
        return view('trackers.dashboard', compact('tracker', 'owner'));
    }
    
    public function destroy(Tracker $tracker)
    {
        $this->authorizeAccess($tracker);
        $tracker->delete(); // Soft delete

        return redirect()->route('trackers.index')->with('success', 'Tracker deleted.');
    }

    private function authorizeAccess(Tracker $tracker)
    {
        // Get the position of the current user in the tracker
        $position = $tracker->users()->where('user_id', auth()->id())->first()?->pivot->position;
        if (!in_array($position, ['owner', 'full'])) {
            abort(403, 'Unauthorized');
        }
    }

    public function showDashboard($trackerId)
    {   
        $tracker = Tracker::findOrFail($trackerId);
    
        // Grouped Expenses for the current month
        $expensesGrouped = $tracker->expenses()
            ->whereMonth('date', now()->month)
            ->get()
            ->groupBy(function ($expense) {
                return \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d');
            });
    
        // Fetch all related models via relationships
        $recurringIncome = $tracker->recurringIncomes;
        $savingGoals = $tracker->savingGoals;
        $expenseCategories = $tracker->expenseCategories;
        $recurringBills = $tracker->recurringBills;
    
        // Get the tracker owner
        $owner = $tracker->users()->wherePivot('position', 'owner')->first();
    
        return view('trackers.dashboard', compact(
            'tracker',
            'expensesGrouped',
            'recurringIncome',
            'savingGoals',
            'expenseCategories',
            'recurringBills',
            'owner'
        ));

        
    }
    
    
    
    

    // Method for adding expenses to a specific date
    public function addExpense(Request $request, $trackerId, $date)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
        ]);
    
        // Create the new expense entry
        Expense::create([
            'tracker_id' => $trackerId,
            'expense_date' => $date,
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);
    
        // Redirect to the tracker dashboard with a success message
        return redirect()->route('trackers.dashboard', $trackerId)
                         ->with('success', 'Expense added successfully!');
    }    

    public function storeRecurringIncome(Request $request, Tracker $tracker)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'interval' => 'required|in:daily,weekly,monthly,yearly',
        ]);
    
        $tracker->recurringIncomes()->create($request->all());
    
        return redirect()->route('trackers.dashboard', $tracker->id)->with('success', 'Recurring Income added.');
    }
    

    public function storeSavingGoal(Request $request, Tracker $tracker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric',
        ]);
    
        $tracker->savingGoals()->create($request->all());
    
        return redirect()->route('trackers.dashboard', $tracker->id)->with('success', 'Saving Goal added.');
    }
    

    public function storeExpenseCategory(Request $request, Tracker $tracker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $tracker->expenseCategories()->create($request->all());
    
        return redirect()->route('trackers.dashboard', $tracker->id)->with('success', 'Expense Category added.');
    }
    
    public function storeRecurringBill(Request $request, Tracker $tracker)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'interval' => 'required|in:daily,weekly,monthly,yearly',
        ]);
    
        $tracker->recurringBills()->create($request->all());
    
        return redirect()->route('trackers.dashboard', $tracker->id)->with('success', 'Recurring Bill added.');
    }
    
// STORE Expense
public function storeExpense(Request $request)
{
    $data = $request->validate([
        'tracker_id' => 'required|exists:trackers,id',
        'user_id' => 'required|exists:users,id',
        'type' => 'required|in:expense,income',
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'date' => 'required|date',
        'category_id' => 'required|exists:expense_categories,id',
        'attachment' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('attachment')) {
        $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
    }

    if ($data['type'] === 'expense') {
        Expense::create($data);
    } else {
        // Create Income (use only required fields for Income)
        Income::create([
            'tracker_id' => $data['tracker_id'],
            'user_id' => $data['user_id'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'date' => $data['date'],
        ]);
    }

    return back()->with('success', 'Transaction added successfully.');
}


// STORE Income
public function storeIncome(Request $request)
{
    $data = $request->validate([
        'tracker_id' => 'required|exists:trackers,id',
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'date' => 'required|date',
    ]);

    Income::create($data);

    return back()->with('success', 'Income added successfully.');
}

// UPDATE Expense
public function updateExpense(Request $request, Expense $expense)
{
    $data = $request->validate([
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'date' => 'required|date',
        'attachment' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('attachment')) {
        $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
    }

    $expense->update($data);

    return back()->with('success', 'Expense updated successfully.');
}

// UPDATE Income
public function updateIncome(Request $request, Income $income)
{
    $data = $request->validate([
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric',
        'date' => 'required|date',
    ]);

    $income->update($data);

    return back()->with('success', 'Income updated successfully.');
}

// DELETE Expense
public function deleteExpense(Expense $expense)
{
    $expense->delete();
    return back()->with('success', 'Expense deleted successfully.');
}

// DELETE Income
public function deleteIncome(Income $income)
{
    $income->delete();
    return back()->with('success', 'Income deleted successfully.');
}


    
}
