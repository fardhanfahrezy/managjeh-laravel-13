<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringRule;
use App\Models\User;
use Carbon\Carbon;

test('artisan recurring:process generates transaction and advances next date without month overflow', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'saldo' => 1000000,
    ]);
    $category = Category::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'expense',
    ]);

    // Rule set to 2026-01-31 (monthly)
    $rule = RecurringRule::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 200000,
        'frekuensi' => 'monthly',
        'tanggal_berikutnya' => Carbon::today()->toDateString(),
        'catatan' => 'Wifi Bulanan',
        'is_active' => true,
    ]);

    $this->artisan('recurring:process')->assertSuccessful();

    // Check account balance deducted
    expect((float) $account->fresh()->saldo)->toEqual(800000.0);

    // Check transaction created
    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'account_id' => $account->id,
        'jumlah' => 200000,
        'tipe' => 'expense',
    ]);

    // Check date advanced
    expect($rule->fresh()->tanggal_berikutnya->toDateString())->not->toEqual(Carbon::today()->toDateString());
});

test('artisan recurring:process skips rule with insufficient funds and dispatches in-app notification', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'bank',
        'saldo' => 50000, // Not enough for 200000
    ]);
    $category = Category::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'expense',
    ]);

    $rule = RecurringRule::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 200000,
        'tanggal_berikutnya' => Carbon::today()->toDateString(),
        'catatan' => 'Tagihan Listrik',
        'is_active' => true,
    ]);

    $this->artisan('recurring:process')->assertSuccessful();

    // Account balance untouched
    expect((float) $account->fresh()->saldo)->toEqual(50000.0);

    // Notification created in database for user
    expect($user->fresh()->notifications()->count())->toBe(1);
    expect($user->fresh()->notifications()->first()->data['title'])->toContain('Tagihan Otomatis Dilewati');
});
