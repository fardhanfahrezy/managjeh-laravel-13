<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->expenseCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'expense',
        'nama' => 'Makanan & Minuman',
    ]);
    $this->incomeCategory = Category::factory()->create([
        'user_id' => $this->user->id,
        'tipe' => 'income',
        'nama' => 'Gaji Pokok',
    ]);
});

test('user can set a monthly budget for an expense category', function () {
    $response = $this->actingAs($this->user)->post(route('budgets.store'), [
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 1500000.00,
        'periode' => '2026-08',
    ]);

    $response->assertRedirect(route('budgets.index', ['periode' => '2026-08']));
    $this->assertDatabaseHas('budgets', [
        'user_id' => $this->user->id,
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 1500000.00,
        'periode' => '2026-08',
    ]);
});

test('user cannot set a budget for an income category', function () {
    $response = $this->actingAs($this->user)->post(route('budgets.store'), [
        'category_id' => $this->incomeCategory->id,
        'limit_bulanan' => 1000000.00,
        'periode' => '2026-08',
    ]);

    $response->assertSessionHasErrors('category_id');
});

test('user cannot create duplicate budget for the same category and period', function () {
    Budget::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 1000000.00,
        'periode' => '2026-08',
    ]);

    $response = $this->actingAs($this->user)->post(route('budgets.store'), [
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 2000000.00,
        'periode' => '2026-08',
    ]);

    $response->assertSessionHasErrors('periode');
});

test('user can update budget limit', function () {
    $budget = Budget::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 1000000.00,
        'periode' => '2026-08',
    ]);

    $response = $this->actingAs($this->user)->put(route('budgets.update', $budget), [
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 2500000.00,
        'periode' => '2026-08',
    ]);

    $response->assertRedirect(route('budgets.index', ['periode' => '2026-08']));
    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'limit_bulanan' => 2500000.00,
    ]);
});

test('user can delete budget', function () {
    $budget = Budget::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->expenseCategory->id,
        'limit_bulanan' => 1000000.00,
        'periode' => '2026-08',
    ]);

    $response = $this->actingAs($this->user)->delete(route('budgets.destroy', $budget));

    $response->assertRedirect(route('budgets.index', ['periode' => '2026-08']));
    $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
});
