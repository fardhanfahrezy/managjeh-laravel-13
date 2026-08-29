<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->account = Account::factory()->create([
        'user_id' => $this->user->id,
        'saldo' => 1000000.00,
    ]);
    $this->category = Category::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'expense',
        'nama' => 'Makan Minum',
    ]);
});

test('user can view dashboard with summary metrics', function () {
    Transaction::factory()->create([
        'user_id' => $this->user->id,
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'jumlah' => 75000.00,
        'tipe' => 'expense',
        'tanggal' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Total Saldo Bersih');
    $response->assertSee('Makan Minum');
});

test('user can view reports page', function () {
    Transaction::factory()->create([
        'user_id' => $this->user->id,
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'jumlah' => 150000.00,
        'tipe' => 'expense',
        'tanggal' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->user)->get(route('reports.index'));

    $response->assertOk();
    $response->assertSee('Laporan Keuangan');
    $response->assertSee('Makan Minum');
});
