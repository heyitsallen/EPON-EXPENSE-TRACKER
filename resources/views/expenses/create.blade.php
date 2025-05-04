@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Transaction</h1>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tracker_id" value="{{ $tracker->id }}">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" class="form-control" required>
                <option value="expense">Expense</option>
                <option value="income">Income</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" class="form-control" required>
                <option value="" disabled selected>Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="attachment">Attachment</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Add Transaction</button>
    </form>
</div>
@endsection
