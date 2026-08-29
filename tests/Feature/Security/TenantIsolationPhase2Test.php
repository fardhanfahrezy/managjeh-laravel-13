<?php

use App\Models\Account;
use App\Models\Goal;
use App\Models\RecurringRule;
use App\Models\User;

test('user A cannot access, edit, or delete user B goal', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $goalB = Goal::factory()->create(['user_id' => $userB->id]);

    // View
    $this->actingAs($userA)->get(route('goals.edit', $goalB))->assertForbidden();

    // Update
    $this->actingAs($userA)->put(route('goals.update', $goalB), [
        'nama_goal' => 'Hacked Goal',
        'target' => 99999999,
    ])->assertForbidden();

    // Delete
    $this->actingAs($userA)->delete(route('goals.destroy', $goalB))->assertForbidden();
});

test('user A cannot deposit into user B goal', function () {
    $userA = User::factory()->create();
    $accountA = Account::factory()->create(['user_id' => $userA->id, 'saldo' => 5000000]);

    $userB = User::factory()->create();
    $goalB = Goal::factory()->create(['user_id' => $userB->id, 'target' => 10000000]);

    $response = $this->actingAs($userA)->post(route('goals.deposit', $goalB), [
        'account_id' => $accountA->id,
        'jumlah' => 1000000,
    ]);

    $response->assertForbidden();
});

test('user A cannot access or modify user B recurring rule', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $accountB = Account::factory()->create(['user_id' => $userB->id]);
    $ruleB = RecurringRule::factory()->create([
        'user_id' => $userB->id,
        'account_id' => $accountB->id,
    ]);

    $this->actingAs($userA)->get(route('recurring-rules.edit', $ruleB))->assertForbidden();
    $this->actingAs($userA)->patch(route('recurring-rules.toggle', $ruleB))->assertForbidden();
    $this->actingAs($userA)->delete(route('recurring-rules.destroy', $ruleB))->assertForbidden();
});
