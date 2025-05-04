@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h4>Manage Expenses & Income for Tracker: {{ $tracker->title }}</h4>
        </div>
        <div class="card-body">
            {{-- Add Transaction Form --}}
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="tracker_id" value="{{ $tracker->id }}">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div class="row g-2">
                    <div class="col-md-2">
                        <select name="type" class="form-select" required>
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                        </select>
                    </div>               
                    <div class="col-md-3" id="newCategoryDiv" style="display: none;">
                        <input type="text" name="new_category" id="newCategoryInput" class="form-control" placeholder="Enter new category name">
                    </div>           
                    <div class="col-md-2">
                        <input type="number" name="amount" class="form-control" placeholder="Amount" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="description" class="form-control" placeholder="Description" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </div>
            </form>

            {{-- Expenses --}}
            <h5>Expenses</h5>
            <ul class="list-group mb-4">
                @forelse($expenses as $expense)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $expense->description }}</strong> - ₱{{ $expense->amount }}
                                <br>
                                <small>{{ \Carbon\Carbon::parse($expense->date)->toFormattedDateString() }}</small>
                                @if($expense->attachment_path)
                                    <br>
                                    <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank">View Receipt</a>
                                @endif
                            </div>
                            <div>
                                {{-- Edit Form (inline) --}}
                                <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="type" value="expense">
                                    <input type="hidden" name="date" value="{{ $expense->date }}">
                                    <input type="text" name="description" value="{{ $expense->description }}" class="form-control d-inline w-50 mb-1">
                                    <input type="number" name="amount" value="{{ $expense->amount }}" step="0.01" class="form-control d-inline w-25 mb-1">
                                    <input type="file" name="attachment" class="form-control d-inline w-50 mb-1">
                                    <button type="submit" class="btn btn-sm btn-success">Update</button>
                                </form>

                                {{-- Delete Form --}}
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="expense">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No expenses found.</li>
                @endforelse
            </ul>

            {{-- Income --}}
            <h5>Income</h5>
            <ul class="list-group">
                @forelse($income as $item)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $item->description }}</strong> - ₱{{ $item->amount }}
                                <br>
                                <small>{{ \Carbon\Carbon::parse($item->date)->toFormattedDateString() }}</small>
                                @if($item->attachment_path)
                                    <br>
                                    <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank">View Attachment</a>
                                @endif
                            </div>
                            <div>
                                {{-- Edit Form (inline) --}}
                                <form action="{{ route('expenses.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="type" value="income">
                                    <input type="hidden" name="date" value="{{ $item->date }}">
                                    <input type="text" name="description" value="{{ $item->description }}" class="form-control d-inline w-50 mb-1">
                                    <input type="number" name="amount" value="{{ $item->amount }}" step="0.01" class="form-control d-inline w-25 mb-1">
                                    <input type="file" name="attachment" class="form-control d-inline w-50 mb-1">
                                    <button type="submit" class="btn btn-sm btn-success">Update</button>
                                </form>

                                {{-- Delete Form --}}
                                <form action="{{ route('expenses.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="type" value="income">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this income?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">No income records found.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <a href="{{ route('trackers.dashboard', ['tracker' => $tracker->id]) }}" class="btn btn-success mb-3">
        ← Go Back to {{ $tracker->title }} Dashboard
    </a>
    
    
    
</div>



</script>

<script>
    document.getElementById('categorySelect').addEventListener('change', function () {
        const newCategoryDiv = document.getElementById('newCategoryDiv');
        if (this.value === 'new') {
            newCategoryDiv.style.display = 'block';
            document.getElementById('newCategoryInput').required = true;
        } else {
            newCategoryDiv.style.display = 'none';
            document.getElementById('newCategoryInput').required = false;
        }
    });
</script>

@endsection
