<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Tracker;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
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
    

}