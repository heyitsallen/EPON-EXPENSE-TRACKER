@extends('layouts.app')

@section('content')
<div class="container">

    {{-- === Dashboard Header === --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Dashboard for Tracker: {{ $tracker->title }}</h4>
            <span><strong>Owner:</strong> {{ $owner ? $owner->name : 'Not available' }}</span>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6 alert alert-info">
                    <h5>Total Income</h5>
                    <p>This Month: <strong>{{ $totalMonthlyIncome }}</strong></p>
                    <p>This Year: <strong>{{ $totalYearlyIncome }}</strong></p>
                </div>
                <div class="col-md-6 alert alert-warning">
                    <h5>Total Expenses</h5>
                    <p>This Month: <strong>{{ $totalMonthlyExpenses }}</strong></p>
                    <p>This Year: <strong>{{ $totalYearlyExpenses }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- === Charts Section === --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="monthlyIncomeChart"></canvas>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="yearlyComparisonChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="quarterlyComparisonChart"></canvas>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <canvas id="descriptionDistributionChart"></canvas>
        </div>
    </div>

    {{-- === Detailed List of Expenses & Incomes (Optional Section) === --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5>Income & Expense Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Income --}}
                <div class="col-md-6">
                    <h6>Incomes</h6>
                    @foreach($incomesGrouped as $date => $incomeList)
                        <p><strong>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</strong></p>
                        <ul class="list-group mb-2">
                            @foreach($incomeList as $income)
                                <li class="list-group-item">
                                    {{ $income->description }} - {{ $income->amount }}
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>

                {{-- Expenses --}}
                <div class="col-md-6">
                    <h6>Expenses</h6>
                    @foreach($expensesGrouped as $date => $expenseList)
                        <p><strong>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</strong></p>
                        <ul class="list-group mb-2">
                            @foreach($expenseList as $expense)
                                <li class="list-group-item">
                                    {{ $expense->description }} - {{ $expense->amount }}
                                    @if($expense->attachment_path)
                                        <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank">View Receipt</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
        <a href="{{ route('expenses.manage', ['tracker' => $tracker->id]) }}" class="btn btn-success mb-4">Manage Expenses & Income</a>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = {!! json_encode($months) !!};
    const incomeData = {!! json_encode($monthlyIncomeData) !!};
    const expenseData = {!! json_encode($monthlyExpenseData) !!};

    const ctxExpenses = document.getElementById('monthlyExpensesChart').getContext('2d');
    new Chart(ctxExpenses, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Expenses',
                data: expenseData,
                backgroundColor: '#f87171'
            }]
        }
    });

    const ctxIncome = document.getElementById('monthlyIncomeChart').getContext('2d');
    new Chart(ctxIncome, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Income',
                data: incomeData,
                backgroundColor: '#34d399'
            }]
        }
    });

    const ctxYearly = document.getElementById('yearlyComparisonChart').getContext('2d');
    new Chart(ctxYearly, {
        type: 'bar',
        data: {
            labels: ['This Year'],
            datasets: [
                {
                    label: 'Income',
                    data: [{{ $totalYearlyIncome }}],
                    backgroundColor: '#60a5fa'
                },
                {
                    label: 'Expenses',
                    data: [{{ $totalYearlyExpenses }}],
                    backgroundColor: '#fbbf24'
                }
            ]
        }
    });

    const quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
    const quarterlyIncome = [
        {{ $tracker->incomes()->whereBetween('date', [now()->startOfYear(), now()->startOfYear()->addMonths(3)])->sum('amount') }},
        {{ $tracker->incomes()->whereBetween('date', [now()->startOfYear()->addMonths(3), now()->startOfYear()->addMonths(6)])->sum('amount') }},
        {{ $tracker->incomes()->whereBetween('date', [now()->startOfYear()->addMonths(6), now()->startOfYear()->addMonths(9)])->sum('amount') }},
        {{ $tracker->incomes()->whereBetween('date', [now()->startOfYear()->addMonths(9), now()->endOfYear()])->sum('amount') }},
    ];
    const quarterlyExpenses = [
        {{ $tracker->expenses()->whereBetween('date', [now()->startOfYear(), now()->startOfYear()->addMonths(3)])->sum('amount') }},
        {{ $tracker->expenses()->whereBetween('date', [now()->startOfYear()->addMonths(3), now()->startOfYear()->addMonths(6)])->sum('amount') }},
        {{ $tracker->expenses()->whereBetween('date', [now()->startOfYear()->addMonths(6), now()->startOfYear()->addMonths(9)])->sum('amount') }},
        {{ $tracker->expenses()->whereBetween('date', [now()->startOfYear()->addMonths(9), now()->endOfYear()])->sum('amount') }},
    ];

    const ctxQuarter = document.getElementById('quarterlyComparisonChart').getContext('2d');
    new Chart(ctxQuarter, {
        type: 'bar',
        data: {
            labels: quarterlyLabels,
            datasets: [
                {
                    label: 'Quarterly Income',
                    data: quarterlyIncome,
                    backgroundColor: '#38bdf8'
                },
                {
                    label: 'Quarterly Expenses',
                    data: quarterlyExpenses,
                    backgroundColor: '#f87171'
                }
            ]
        }
    });

    const topDescriptions = {!! json_encode($topDescriptions->pluck('description')) !!};
    const topAmounts = {!! json_encode($topDescriptions->pluck('total')) !!};

    const ctxPie = document.getElementById('descriptionDistributionChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: topDescriptions,
            datasets: [{
                label: 'Top Expenses by Description',
                data: topAmounts,
                backgroundColor: ['#f87171', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa']
            }]
        }
    });
</script>


@endsection
