<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringRule;
use App\Models\User;

test('user can view recurring rules list', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id]);
    $rule = RecurringRule::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'catatan' => 'Langganan Spotify',
    ]);

    $response = $this->actingAs($user)->get(route('recurring-rules.index'));

    $response->assertOk();
    $response->assertSee('Langganan Spotify');
});

test('user can create a recurring rule', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'tipe' => 'expense']);

    $response = $this->actingAs($user)->post(route('recurring-rules.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'jumlah' => 186000,
        'frekuensi' => 'monthly',
        'tanggal_berikutnya' => '2026-09-01',
        'catatan' => 'Langganan Netflix Premium',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('recurring-rules.index'));
    $this->assertDatabaseHas('recurring_rules', [
        'user_id' => $user->id,
        'catatan' => 'Langganan Netflix Premium',
        'jumlah' => 186000,
        'frekuensi' => 'monthly',
    ]);
});

test('user can toggle active status of recurring rule', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id]);
    $rule = RecurringRule::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->patch(route('recurring-rules.toggle', $rule));

    $response->assertRedirect(route('recurring-rules.index'));
    expect($rule->fresh()->is_active)->toBeFalse();
});
