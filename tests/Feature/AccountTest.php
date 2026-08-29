<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can view accounts list', function () {
    Account::factory()->create(['user_id' => $this->user->id, 'nama_akun' => 'Dompet Saya']);

    $response = $this->actingAs($this->user)->get(route('accounts.index'));

    $response->assertOk();
    $response->assertSee('Dompet Saya');
});

test('user can create an account with initial balance', function () {
    $response = $this->actingAs($this->user)->post(route('accounts.store'), [
        'nama_akun' => 'BCA Tabungan',
        'tipe' => 'bank',
        'saldo' => 1500000,
        'warna' => '#3B82F6',
        'catatan' => 'Rekening utama',
    ]);

    $response->assertRedirect(route('accounts.index'));
    $this->assertDatabaseHas('accounts', [
        'user_id' => $this->user->id,
        'nama_akun' => 'BCA Tabungan',
        'tipe' => 'bank',
        'saldo' => 1500000.00,
    ]);
});

test('bank, e-wallet, and kas cannot be created with negative initial balance', function () {
    $response = $this->actingAs($this->user)->post(route('accounts.store'), [
        'nama_akun' => 'Kas Minus',
        'tipe' => 'kas',
        'saldo' => -50000,
    ]);

    $response->assertSessionHasErrors('saldo');
});

test('credit card can be created with negative initial balance', function () {
    $response = $this->actingAs($this->user)->post(route('accounts.store'), [
        'nama_akun' => 'Kartu Kredit Mandiri',
        'tipe' => 'kartu_kredit',
        'saldo' => -2500000,
    ]);

    $response->assertRedirect(route('accounts.index'));
    $this->assertDatabaseHas('accounts', [
        'user_id' => $this->user->id,
        'nama_akun' => 'Kartu Kredit Mandiri',
        'tipe' => 'kartu_kredit',
        'saldo' => -2500000.00,
    ]);
});

test('user can update account name and type', function () {
    $account = Account::factory()->create([
        'user_id' => $this->user->id,
        'nama_akun' => 'Old Name',
        'tipe' => 'kas',
        'saldo' => 50000,
    ]);

    $response = $this->actingAs($this->user)->put(route('accounts.update', $account), [
        'nama_akun' => 'New Name',
        'tipe' => 'e-wallet',
    ]);

    $response->assertRedirect(route('accounts.index'));
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'nama_akun' => 'New Name',
        'tipe' => 'e-wallet',
        'saldo' => 50000.00, // Saldo untouched on update
    ]);
});

test('account with transactions cannot be deleted', function () {
    $account = Account::factory()->create(['user_id' => $this->user->id]);
    $category = Category::factory()->create(['user_id' => $this->user->id, 'tipe' => 'expense']);

    Transaction::factory()->create([
        'user_id' => $this->user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 10000,
    ]);

    $response = $this->actingAs($this->user)->delete(route('accounts.destroy', $account));

    $response->assertRedirect(route('accounts.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('accounts', ['id' => $account->id]);
});

test('account without transactions can be deleted', function () {
    $account = Account::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->delete(route('accounts.destroy', $account));

    $response->assertRedirect(route('accounts.index'));
    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
});
