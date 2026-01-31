<?php

use App\Models\User;

test('users can authenticate using the login screen via API', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout via API', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/logout');

    $this->assertGuest();
    $response->assertNoContent();
});
