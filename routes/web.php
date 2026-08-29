<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringRuleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionExportImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('accounts', AccountController::class);
    Route::resource('categories', CategoryController::class);

    // Transaction Import & Export (Must precede resource route)
    Route::get('/transactions/export/csv', [TransactionExportImportController::class, 'export'])->name('transactions.export');
    Route::get('/transactions/export/template', [TransactionExportImportController::class, 'template'])->name('transactions.template');
    Route::post('/transactions/import/csv', [TransactionExportImportController::class, 'import'])->name('transactions.import');

    Route::resource('transactions', TransactionController::class);
    Route::resource('budgets', BudgetController::class);

    // Goals (Financial Goals)
    Route::resource('goals', GoalController::class);
    Route::post('/goals/{goal}/deposit', [GoalController::class, 'deposit'])->name('goals.deposit');
    Route::post('/goals/{goal}/withdraw', [GoalController::class, 'withdraw'])->name('goals.withdraw');

    // Recurring Rules
    Route::resource('recurring-rules', RecurringRuleController::class);
    Route::patch('/recurring-rules/{recurring_rule}/toggle', [RecurringRuleController::class, 'toggle'])->name('recurring-rules.toggle');
    Route::post('/recurring-rules/process-now', [RecurringRuleController::class, 'processNow'])->name('recurring-rules.process-now');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
