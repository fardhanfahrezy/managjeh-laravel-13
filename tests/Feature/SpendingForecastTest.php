<?php

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ForecastService;
use Carbon\Carbon;

test('forecast returns insufficient data when day of month is less than 3', function () {
    $user = User::factory()->create();

    $forecastService = new ForecastService;

    // Simulate Day 2 of current month
    $date = Carbon::now()->startOfMonth()->addDay(); // Day 2
    $result = $forecastService->getMonthlyForecast($user, $date);

    expect($result['has_enough_data'])->toBeFalse();
    expect($result['status'])->toBe('insufficient_data');
});

test('forecast calculates run rate and threshold status correctly on or after day 3', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'saldo' => 10000000]);
    $category = Category::factory()->create(['user_id' => $user->id, 'tipe' => 'expense']);

    // Budget limit: 3,000,000 for current month
    $now = Carbon::create(2026, 8, 10); // Day 10 of August 2026
    Budget::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'limit_bulanan' => 3000000,
        'periode' => '2026-08',
    ]);

    // Spent 1,000,000 in first 10 days => Daily rate = 100,000 => Projected for 31 days = 3,100,000 (> 100% of 3,000,000 limit)
    Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'tipe' => 'expense',
        'jumlah' => 1000000,
        'tanggal' => '2026-08-05',
    ]);

    $forecastService = new ForecastService;
    $result = $forecastService->getMonthlyForecast($user, $now);

    expect($result['has_enough_data'])->toBeTrue();
    expect($result['days_elapsed'])->toBe(10);
    expect($result['daily_rate'])->toBe(100000.0);
    expect($result['projected_total'])->toBe(3100000.0);
    expect($result['status'])->toBe('over_budget');
    expect($result['status_label'])->toBe('Potensi Over Budget');
});

test('forecast data is strictly isolated between users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $accountA = Account::factory()->create(['user_id' => $userA->id, 'saldo' => 5000000]);
    $categoryA = Category::factory()->create(['user_id' => $userA->id, 'tipe' => 'expense']);

    $now = Carbon::create(2026, 8, 15);

    // User A has 2,000,000 expenses
    Transaction::factory()->create([
        'user_id' => $userA->id,
        'account_id' => $accountA->id,
        'category_id' => $categoryA->id,
        'tipe' => 'expense',
        'jumlah' => 2000000,
        'tanggal' => '2026-08-05',
    ]);

    $forecastService = new ForecastService;

    // User B has zero transactions
    $resultB = $forecastService->getMonthlyForecast($userB, $now);

    expect($resultB['has_enough_data'])->toBeTrue();
    expect($resultB['current_expense'])->toBe(0.0);
    expect($resultB['projected_total'])->toBe(0.0);
});
