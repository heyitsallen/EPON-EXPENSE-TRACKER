<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DashboardController;

Route::get('/', fn () => view('welcome'))->name('home');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authenticated routes

Route::middleware(['auth'])->group(function () {
    // Trackers
    Route::get('/dashboard', [TrackerController::class, 'index'])->name('dashboard'); // This is your main dashboard route
    Route::get('/trackers/create', [TrackerController::class, 'create'])->name('trackers.create');
    Route::post('/trackers', [TrackerController::class, 'store'])->name('trackers.store');
    Route::patch('/trackers/{tracker}', [TrackerController::class, 'update'])->name('trackers.update');
    Route::resource('trackers', TrackerController::class)->except(['show']);

    // Dashboard view specific to tracker
    Route::get('/trackers/{tracker}/dashboard', [TrackerController::class, 'showDashboard'])->name('trackers.dashboard'); // This is a tracker-specific dashboard

    // Tracker access management
    Route::post('/trackers/{tracker}/share', [TrackerController::class, 'share'])->name('trackers.share');
    Route::patch('/trackers/{tracker}/access/{user}', [TrackerController::class, 'updateAccess'])->name('trackers.updateAccess');
    Route::delete('/trackers/{tracker}/access/{user}', [TrackerController::class, 'removeAccess'])->name('trackers.removeAccess');

    // Expense management
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store'); // Store expense
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update'); // Update expense (add this line)
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy'); // Delete expense

    // Expense management for a specific tracker
    Route::post('/trackers/{tracker}/add-expense/{date}', [TrackerController::class, 'addExpense'])->name('trackers.addExpense');

    // Other tracker-related features
    Route::post('/trackers/{tracker}/recurring-income', [TrackerController::class, 'storeRecurringIncome'])->name('trackers.recurringIncome');
    Route::post('/trackers/{tracker}/saving-goal', [TrackerController::class, 'storeSavingGoal'])->name('trackers.savingGoal');
    Route::post('/trackers/{tracker}/expense-category', [TrackerController::class, 'storeExpenseCategory'])->name('trackers.expenseCategory');
    Route::post('/trackers/{tracker}/recurring-bill', [TrackerController::class, 'storeRecurringBill'])->name('trackers.recurringBill');
    
    // Manage Expenses page for a tracker
    Route::get('/trackers/{tracker}/manage', [ExpenseController::class, 'manage'])->name('expenses.manage');
    
    // Create expense for a specific tracker
    Route::get('/tracker/{tracker}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::put('/trackers/{tracker}/income/{income}', [IncomeController::class, 'update'])->name('income.update');
    Route::delete('/trackers/{tracker}/income/{income}', [IncomeController::class, 'destroy'])->name('income.destroy');
    

});
