<?php

use App\Models\Account;
use App\Models\Goal;
use App\Models\User;

test('user can view goals list', function () {
    $user = User::factory()->create();
    $goal = Goal::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('goals.index'));

    $response->assertOk();
    $response->assertSee($goal->nama_goal);
});

test('user can create a financial goal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('goals.store'), [
        'nama_goal' => 'Dana Darurat 2026',
        'target' => 20000000,
        'deadline' => '2026-12-31',
        'catatan' => 'Target tabungan 6 bulan pengeluaran',
    ]);

    $response->assertRedirect(route('goals.index'));
    $this->assertDatabaseHas('goals', [
        'user_id' => $user->id,
        'nama_goal' => 'Dana Darurat 2026',
        'target' => 20000000,
        'progres' => 0,
    ]);
});

test('user can deposit into goal which deducts account balance and increments goal progress', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'bank',
        'saldo' => 5000000,
    ]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'target' => 10000000,
        'progres' => 1000000,
    ]);

    $response = $this->actingAs($user)->post(route('goals.deposit', $goal), [
        'account_id' => $account->id,
        'jumlah' => 2000000,
        'catatan' => 'Nabung bonus kerja',
    ]);

    $response->assertRedirect(route('goals.index'));

    // Check account balance deducted
    expect((float) $account->fresh()->saldo)->toEqual(3000000.0);

    // Check goal progress increased
    expect((float) $goal->fresh()->progres)->toEqual(3000000.0);

    // Check transaction created with saving type
    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'account_id' => $account->id,
        'goal_id' => $goal->id,
        'tipe' => 'saving',
        'jumlah' => 2000000,
    ]);
});

test('deposit exceeding bank account balance is rejected', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'bank',
        'saldo' => 500000,
    ]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'target' => 5000000,
        'progres' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('goals.deposit', $goal), [
        'account_id' => $account->id,
        'jumlah' => 1000000,
    ]);

    $response->assertSessionHasErrors('jumlah');
    expect((float) $account->fresh()->saldo)->toEqual(500000.0);
    expect((float) $goal->fresh()->progres)->toEqual(0.0);
});

test('user can withdraw from goal which increases account balance and decreases goal progress', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'tipe' => 'bank',
        'saldo' => 1000000,
    ]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'target' => 10000000,
        'progres' => 5000000,
    ]);

    $response = $this->actingAs($user)->post(route('goals.withdraw', $goal), [
        'account_id' => $account->id,
        'jumlah' => 2000000,
        'catatan' => 'Tarik sebagian untuk kebutuhan mendesak',
    ]);

    $response->assertRedirect(route('goals.index'));

    expect((float) $account->fresh()->saldo)->toEqual(3000000.0);
    expect((float) $goal->fresh()->progres)->toEqual(3000000.0);
});

test('withdraw exceeding goal collected progress is rejected', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'saldo' => 1000000,
    ]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'target' => 10000000,
        'progres' => 500000,
    ]);

    $response = $this->actingAs($user)->post(route('goals.withdraw', $goal), [
        'account_id' => $account->id,
        'jumlah' => 1000000,
    ]);

    $response->assertSessionHasErrors('jumlah');
    expect((float) $account->fresh()->saldo)->toEqual(1000000.0);
    expect((float) $goal->fresh()->progres)->toEqual(500000.0);
});
