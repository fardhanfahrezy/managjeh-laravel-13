<?php

use App\Models\User;

test('authenticated pages render dark mode script and toggle elements', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee("localStorage.theme === 'dark'", false);
    $response->assertSee("document.documentElement.classList.add('dark')", false);
    $response->assertSee('aria-label="Toggle theme"', false);
    $response->assertSee('dark:bg-slate-950', false);
});

test('guest pages render dark mode initialization script', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee("localStorage.theme === 'dark'", false);
    $response->assertSee("document.documentElement.classList.add('dark')", false);
    $response->assertSee('dark:bg-gray-800', false);
});

test('navigation bar contains mobile dark mode toggle option', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Mode Tampilan');
    $response->assertSee('theme-changed');
});
