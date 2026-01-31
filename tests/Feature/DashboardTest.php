<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest users are redirected to login page', function () {
    $response = $this->get('/dashboard');
    
    $response->assertRedirect('/login');
});

test('authenticated users can view dashboard', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertStatus(200)
             ->assertSee('Dashboard')
             ->assertSee($user->name);
});

test('dashboard displays ticket statistics', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    // Create tickets with different statuses
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'status' => 'open'
    ]);
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'status' => 'in_progress'
    ]);
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'status' => 'resolved'
    ]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertStatus(200)
             ->assertSee('Total Tickets')
             ->assertSee('3') // Total
             ->assertSee('1'); // Open, in progress, resolved counts
});

test('dashboard shows recent tickets', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Recent Ticket Title'
    ]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertStatus(200)
             ->assertSee('Recent Ticket Title')
             ->assertSee($category->name);
});

test('dashboard does not show other users tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    
    Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Other Users Ticket'
    ]);
    
    $response = $this->actingAs($user)->get('/dashboard');
    
    $response->assertStatus(200)
             ->assertDontSee('Other Users Ticket');
});
