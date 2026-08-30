<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users get default account and categories seeded automatically', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $user = User::where('email', 'john@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->accounts()->count())->toBe(3);
    expect($user->accounts()->where('nama_akun', 'Kas / Dompet Tunai')->exists())->toBeTrue();
    expect($user->categories()->count())->toBe(12);
});
