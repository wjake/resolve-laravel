<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can view login page', function () {
    $response = $this->get('/login');
    
    $response->assertStatus(200)
             ->assertSee('Sign in to your account')
             ->assertSee('Email Address')
             ->assertSee('Password');
});

test('guest can view register page', function () {
    $response = $this->get('/register');
    
    $response->assertStatus(200)
             ->assertSee('Create your account')
             ->assertSee('Full Name')
             ->assertSee('Email Address');
});

test('user can register via web', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertRedirect('/dashboard');
    
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User'
    ]);
    
    $this->assertAuthenticated();
});

test('user can login via web', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);
    
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password'
    ]);
    
    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);
    
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password'
    ]);
    
    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('user can logout', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user);
    $this->assertAuthenticated();
    
    $response = $this->post('/logout');
    
    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('authenticated user is redirected from login page', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/login');
    
    $response->assertRedirect('/dashboard');
});

test('registration requires name', function () {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertSessionHasErrors('name');
});

test('registration requires valid email', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('registration requires password confirmation', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password'
    ]);
    
    $response->assertSessionHasErrors('password');
});

test('registration requires unique email', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertSessionHasErrors('email');
});
