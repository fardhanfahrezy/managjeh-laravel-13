<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->account = Account::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'bank',
        'saldo' => 500000.00,
    ]);
    $this->destAccount = Account::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'e-wallet',
        'saldo' => 100000.00,
    ]);
    $this->creditAccount = Account::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'kartu_kredit',
        'saldo' => 0.00,
    ]);
    $this->incomeCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'income',
    ]);
    $this->expenseCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'expense',
    ]);
});

test('user can record income which increases account balance', function () {
    $response = $this->actingAs($this->user)->post(route('transactions.store'), [
        'tipe' => 'income',
        'account_id' => $this->account->id,
        'category_id' => $this->incomeCategory->id,
        'jumlah' => 250000.00,
        'tanggal' => now()->toDateString(),
        'catatan' => 'Gaji sampingan',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(750000.00);
});

test('user can record expense which decreases account balance', function () {
    $response = $this->actingAs($this->user)->post(route('transactions.store'), [
        'tipe' => 'expense',
        'account_id' => $this->account->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 150000.00,
        'tanggal' => now()->toDateString(),
        'catatan' => 'Beli perlengkapan',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(350000.00);
});

test('expense exceeding balance on bank account is rejected', function () {
    $response = $this->actingAs($this->user)->post(route('transactions.store'), [
        'tipe' => 'expense',
        'account_id' => $this->account->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 600000.00, // exceeds 500,000
        'tanggal' => now()->toDateString(),
    ]);

    $response->assertSessionHasErrors('jumlah');
    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(500000.00);
});

test('expense exceeding balance on credit card is allowed', function () {
    $response = $this->actingAs($this->user)->post(route('transactions.store'), [
        'tipe' => 'expense',
        'account_id' => $this->creditAccount->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 500000.00,
        'tanggal' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->creditAccount->refresh();
    expect((float) $this->creditAccount->saldo)->toBe(-500000.00);
});

test('transfer decreases source account and increases destination account', function () {
    $response = $this->actingAs($this->user)->post(route('transactions.store'), [
        'tipe' => 'transfer',
        'account_id' => $this->account->id,
        'destination_account_id' => $this->destAccount->id,
        'jumlah' => 200000.00,
        'tanggal' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->account->refresh();
    $this->destAccount->refresh();

    expect((float) $this->account->saldo)->toBe(300000.00);
    expect((float) $this->destAccount->saldo)->toBe(300000.00);
});

test('updating transaction adjusts account balances properly', function () {
    // Initial expense: 100,000 -> saldo becomes 400,000
    $tx = app(TransactionService::class)->createTransaction($this->user, [
        'tipe' => 'expense',
        'account_id' => $this->account->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 100000.00,
        'tanggal' => now()->toDateString(),
    ]);

    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(400000.00);

    // Update expense to 150,000 -> saldo should adjust to 350,000
    $response = $this->actingAs($this->user)->put(route('transactions.update', $tx), [
        'tipe' => 'expense',
        'account_id' => $this->account->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 150000.00,
        'tanggal' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(350000.00);
});

test('deleting transaction restores account balance and soft deletes record', function () {
    $tx = app(TransactionService::class)->createTransaction($this->user, [
        'tipe' => 'expense',
        'account_id' => $this->account->id,
        'category_id' => $this->expenseCategory->id,
        'jumlah' => 200000.00,
        'tanggal' => now()->toDateString(),
    ]);

    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(300000.00);

    $response = $this->actingAs($this->user)->delete(route('transactions.destroy', $tx));

    $response->assertRedirect(route('transactions.index'));
    $this->account->refresh();
    expect((float) $this->account->saldo)->toBe(500000.00); // Restored!

    // Soft delete check
    expect(Transaction::withTrashed()->where('id', $tx->id)->exists())->toBeTrue();
    expect(Transaction::where('id', $tx->id)->exists())->toBeFalse();
});
