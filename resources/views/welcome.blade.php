@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-8 text-center">
            <div class="card shadow-lg p-4">
                <!-- Logo Section -->
                <div class="mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 200px;">
                </div>

                <div class="card-header text-center">
                    <h1 class="display-4 font-weight-bold">Welcome to E-PON expense tracker!</h1>
                </div>

                <div class="card-body">
                    <p class="lead mb-4">Take control of your finances today! This platform is designed to help you save, track, and achieve your financial goals. Here's how you can get started:</p>

                    <!-- Motivational Text -->
                    <div class="mb-4">
                        <p class="h4 text-primary font-weight-bold">Save Wisely</p>
                        <p>Start saving today! Set a goal, track your progress, and watch your savings grow over time.</p>

                        <p class="h4 text-success font-weight-bold">Track Your Expenses</p>
                        <p>Keep an eye on where your money goes each month. Categorize your expenses to find where you can save more.</p>

                        <p class="h4 text-warning font-weight-bold">Set Goals and Achieve Them</p>
                        <p>Whether it's for an emergency fund or a vacation, setting clear financial goals helps you stay on track.</p>
                    </div>

                    <!-- Call to Action Button -->
                    <a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary">Start Tracking Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional Footer Section -->
<footer class="bg-light text-dark py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">© 2025 EPON expense tracker. All rights reserved.</p>
    </div>
</footer>

@endsection
