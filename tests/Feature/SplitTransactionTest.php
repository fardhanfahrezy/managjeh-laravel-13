<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\User;

test('user can create transaction with split categories', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'saldo' => 1000000]);
    $cat1 = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Makanan', 'tipe' => 'expense']);
    $cat2 = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Belanja', 'tipe' => 'expense']);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'tipe' => 'expense',
        'jumlah' => 500000,
        'tanggal' => '2026-08-29',
        'catatan' => 'Belanja di supermarket',
        'splits' => [
            ['category_id' => $cat1->id, 'jumlah' => 300000, 'catatan' => 'Groceries'],
            ['category_id' => $cat2->id, 'jumlah' => 200000, 'catatan' => 'Peralatan rumah'],
        ],
    ]);

    $response->assertRedirect(route('transactions.index'));

    expect((float) $account->fresh()->saldo)->toEqual(500000.0);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'account_id' => $account->id,
        'jumlah' => 500000,
    ]);

    $this->assertDatabaseHas('transaction_splits', [
        'category_id' => $cat1->id,
        'jumlah' => 300000,
    ]);

    $this->assertDatabaseHas('transaction_splits', [
        'category_id' => $cat2->id,
        'jumlah' => 200000,
    ]);
});

test('split transaction with mismatched total is rejected', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'saldo' => 1000000]);
    $cat1 = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Makanan 1', 'tipe' => 'expense']);
    $cat2 = Category::factory()->create(['user_id' => $user->id, 'nama' => 'Makanan 2', 'tipe' => 'expense']);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'tipe' => 'expense',
        'jumlah' => 500000,
        'tanggal' => '2026-08-29',
        'splits' => [
            ['category_id' => $cat1->id, 'jumlah' => 300000],
            ['category_id' => $cat2->id, 'jumlah' => 100000], // sum = 400k != 500k
        ],
    ]);

    $response->assertSessionHasErrors('splits');
    expect((float) $account->fresh()->saldo)->toEqual(1000000.0);
});
