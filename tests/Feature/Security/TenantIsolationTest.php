<?php

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->userA = User::factory()->create();
    $this->userB = User::factory()->create();

    $this->accountA = Account::factory()->create(['user_id' => $this->userA->id, 'saldo' => 500000]);
    $this->accountB = Account::factory()->create(['user_id' => $this->userB->id, 'saldo' => 1000000]);

    $this->categoryA = Category::factory()->create(['user_id' => $this->userA->id, 'tipe' => 'expense']);
    $this->categoryB = Category::factory()->create(['user_id' => $this->userB->id, 'tipe' => 'expense']);

    $this->transactionB = Transaction::factory()->create([
        'user_id' => $this->userB->id,
        'account_id' => $this->accountB->id,
        'category_id' => $this->categoryB->id,
        'jumlah' => 50000,
        'tipe' => 'expense',
    ]);

    $this->budgetB = Budget::factory()->create([
        'user_id' => $this->userB->id,
        'category_id' => $this->categoryB->id,
        'limit_bulanan' => 1000000,
        'periode' => now()->format('Y-m'),
    ]);
});

test('user A cannot access or edit user B account', function () {
    $response = $this->actingAs($this->userA)->get(route('accounts.edit', $this->accountB));
    $response->assertForbidden();

    $updateResponse = $this->actingAs($this->userA)->put(route('accounts.update', $this->accountB), [
        'nama_akun' => 'Hacked Account Name',
        'tipe' => 'bank',
    ]);
    $updateResponse->assertForbidden();

    $deleteResponse = $this->actingAs($this->userA)->delete(route('accounts.destroy', $this->accountB));
    $deleteResponse->assertForbidden();
});

test('user A cannot access or edit user B category', function () {
    $response = $this->actingAs($this->userA)->get(route('categories.edit', $this->categoryB));
    $response->assertForbidden();

    $updateResponse = $this->actingAs($this->userA)->put(route('categories.update', $this->categoryB), [
        'nama' => 'Hacked Category',
        'tipe' => 'expense',
    ]);
    $updateResponse->assertForbidden();

    $deleteResponse = $this->actingAs($this->userA)->delete(route('categories.destroy', $this->categoryB));
    $deleteResponse->assertForbidden();
});

test('user A cannot access or edit user B transaction', function () {
    $response = $this->actingAs($this->userA)->get(route('transactions.edit', $this->transactionB));
    $response->assertForbidden();

    $updateResponse = $this->actingAs($this->userA)->put(route('transactions.update', $this->transactionB), [
        'account_id' => $this->accountA->id,
        'category_id' => $this->categoryA->id,
        'jumlah' => 10000,
        'tipe' => 'expense',
        'tanggal' => now()->toDateString(),
    ]);
    $updateResponse->assertForbidden();

    $deleteResponse = $this->actingAs($this->userA)->delete(route('transactions.destroy', $this->transactionB));
    $deleteResponse->assertForbidden();
});

test('user A cannot access or edit user B budget', function () {
    $response = $this->actingAs($this->userA)->get(route('budgets.edit', $this->budgetB));
    $response->assertForbidden();

    $updateResponse = $this->actingAs($this->userA)->put(route('budgets.update', $this->budgetB), [
        'category_id' => $this->categoryA->id,
        'limit_bulanan' => 500000,
        'periode' => now()->format('Y-m'),
    ]);
    $updateResponse->assertForbidden();

    $deleteResponse = $this->actingAs($this->userA)->delete(route('budgets.destroy', $this->budgetB));
    $deleteResponse->assertForbidden();
});

test('user A cannot create transaction using user B account or category', function () {
    // Attempt with User B's account
    $response = $this->actingAs($this->userA)->post(route('transactions.store'), [
        'tipe' => 'expense',
        'account_id' => $this->accountB->id,
        'category_id' => $this->categoryA->id,
        'jumlah' => 50000,
        'tanggal' => now()->toDateString(),
    ]);
    $response->assertSessionHasErrors('account_id');

    // Attempt with User B's category
    $responseCat = $this->actingAs($this->userA)->post(route('transactions.store'), [
        'tipe' => 'expense',
        'account_id' => $this->accountA->id,
        'category_id' => $this->categoryB->id,
        'jumlah' => 50000,
        'tanggal' => now()->toDateString(),
    ]);
    $responseCat->assertSessionHasErrors('category_id');

    // Attempt transfer with User B's destination account
    $responseTransfer = $this->actingAs($this->userA)->post(route('transactions.store'), [
        'tipe' => 'transfer',
        'account_id' => $this->accountA->id,
        'destination_account_id' => $this->accountB->id,
        'jumlah' => 50000,
        'tanggal' => now()->toDateString(),
    ]);
    $responseTransfer->assertSessionHasErrors('destination_account_id');
});
