@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Dashboard for Tracker: {{ $tracker->title }}</h4>
        @if($owner)
            <span><strong>Owner:</strong> {{ $owner->name }}</span>
        @else
            <span><strong>Owner:</strong> Not available</span>
        @endif
    </div>

    <div class="card-body">
        <p>Welcome to the dashboard for this tracker!</p>

        {{-- === Recurring Income Section === --}}
        <div class="mb-4">
            <h5 class="mt-4">Recurring Income</h5>
            <ul class="list-group">
                @foreach($recurringIncome as $income)
                    <li class="list-group-item">
                        {{ $income->description }}: {{ $income->amount }} ({{ $income->interval }})
                    </li>
                @endforeach
            </ul>

            @php
                $yearlyIncome = $recurringIncome->where('interval', 'yearly')->sum('amount');
                $monthlyIncome = $recurringIncome->where('interval', 'monthly')->sum('amount') * 12;
                $totalIncome = $yearlyIncome + $monthlyIncome;
            @endphp

            <div class="alert alert-info mt-3">
                <strong>Total Income (Year):</strong> {{ $totalIncome }} <br>
                <strong>Monthly vs Yearly:</strong> {{ $monthlyIncome }} vs {{ $yearlyIncome }}
            </div>
        </div>

        {{-- === Saving Goals Section === --}}
        <div class="mb-4">
            <h5 class="mt-4">Saving Goals</h5>
            <ul class="list-group">
                @foreach($savingGoals as $goal)
                    <li class="list-group-item">
                        {{ $goal->name }}: Target - {{ $goal->target_amount }} | Progress - {{ $goal->progress }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- === Expense Categories Section === --}}
        <div class="mb-4">
            <h5 class="mt-4">Expense Categories</h5>
            <ul class="list-group">
                @foreach($expenseCategories as $category)
                    <li class="list-group-item">{{ $category->name }}</li>
                @endforeach
            </ul>
        </div>

        {{-- === Recurring Bills Section === --}}
        <div class="mb-4">
            <h5 class="mt-4">Recurring Bills</h5>
            <ul class="list-group">
                @foreach($recurringBills as $bill)
                    <li class="list-group-item">
                        {{ $bill->description }}: {{ $bill->amount }} ({{ $bill->interval }})
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- === Expenses Section === --}}
        <div class="mb-4">
            <h5 class="mt-4">Expenses</h5>

            @php
                $totalMonth = $tracker->expenses()->whereMonth('date', now()->month)->sum('amount');
                $totalYear = $tracker->expenses()->whereYear('date', now()->year)->sum('amount');
            @endphp

            <div class="alert alert-warning">
                <strong>Total Expenses (This Month):</strong> {{ $totalMonth }} <br>
                <strong>Total Expenses (This Year):</strong> {{ $totalYear }}
            </div>

            @foreach($expensesGrouped as $date => $expenseList)
                <h6>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
                <ul class="list-group">
                    @foreach($expenseList as $expense)
                        <li class="list-group-item">
                            {{ $expense->description }} - {{ $expense->amount }}
                            @if($expense->attachment_path)
                                <a href="{{ asset($expense->attachment_path) }}" target="_blank" class="btn btn-link">View Receipt</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
        <a href="{{ route('expenses.manage', ['tracker' => $tracker->id]) }}" class="btn btn-success mb-4">Manage Expenses & Income</a>
    </div>
</div>

@endsection
