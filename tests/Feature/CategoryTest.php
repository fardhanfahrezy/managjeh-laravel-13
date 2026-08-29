<?php

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can view categories list', function () {
    Category::factory()->create(['user_id' => $this->user->id, 'nama' => 'Makanan Enak', 'tipe' => 'expense']);

    $response = $this->actingAs($this->user)->get(route('categories.index'));

    $response->assertOk();
    $response->assertSee('Makanan Enak');
});

test('user can create category', function () {
    $response = $this->actingAs($this->user)->post(route('categories.store'), [
        'nama' => 'Investasi Saham',
        'tipe' => 'income',
        'warna' => '#10B981',
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', [
        'user_id' => $this->user->id,
        'nama' => 'Investasi Saham',
        'tipe' => 'income',
    ]);
});

test('user can update category', function () {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'nama' => 'Old Category',
        'tipe' => 'expense',
    ]);

    $response = $this->actingAs($this->user)->put(route('categories.update', $category), [
        'nama' => 'Updated Category',
        'tipe' => 'expense',
        'warna' => '#EF4444',
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'nama' => 'Updated Category',
    ]);
});

test('category with transactions cannot be deleted', function () {
    $account = Account::factory()->create(['user_id' => $this->user->id]);
    $category = Category::factory()->create(['user_id' => $this->user->id, 'tipe' => 'expense']);

    Transaction::factory()->create([
        'user_id' => $this->user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 20000,
    ]);

    $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('category with budgets cannot be deleted', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id, 'tipe' => 'expense']);

    Budget::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('unused category can be deleted', function () {
    $category = Category::factory()->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->delete(route('categories.destroy', $category));

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
