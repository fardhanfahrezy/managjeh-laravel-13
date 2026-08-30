<?php

use App\Models\Account;
use App\Models\User;
use App\Services\FinancialHealthService;

test('authenticated user can view financial health page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('financial-health.index'));

    $response->assertOk();
    $response->assertSee('Kesehatan Finansial &amp; Forecast', false);
});

test('financial health liquid balance excludes credit card accounts', function () {
    $user = User::factory()->create();

    // Create bank, e-wallet, kas, and credit card accounts
    Account::factory()->create(['user_id' => $user->id, 'tipe' => 'bank', 'saldo' => 5000000]);
    Account::factory()->create(['user_id' => $user->id, 'tipe' => 'e-wallet', 'saldo' => 1000000]);
    Account::factory()->create(['user_id' => $user->id, 'tipe' => 'kas', 'saldo' => 500000]);
    Account::factory()->create(['user_id' => $user->id, 'tipe' => 'kartu_kredit', 'saldo' => 2000000]); // Excluded from liquid balance

    $service = new FinancialHealthService;
    $analysis = $service->getHealthAnalysis($user);

    // Liquid balance should be 5M + 1M + 0.5M = 6.5M (excluding 2M credit card)
    expect($analysis['liquid_balance'])->toBe(6500000.0);
    expect($analysis['credit_card_balance'])->toBe(2000000.0);
});

test('financial health analysis is strictly isolated between users', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Account::factory()->create(['user_id' => $userA->id, 'tipe' => 'bank', 'saldo' => 50000000]);
    Account::factory()->create(['user_id' => $userB->id, 'tipe' => 'bank', 'saldo' => 1000000]);

    $service = new FinancialHealthService;

    $analysisA = $service->getHealthAnalysis($userA);
    $analysisB = $service->getHealthAnalysis($userB);

    expect($analysisA['liquid_balance'])->toBe(50000000.0);
    expect($analysisB['liquid_balance'])->toBe(1000000.0);
});
