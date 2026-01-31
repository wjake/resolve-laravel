<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can view tickets index', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Ticket'
    ]);
    
    $response = $this->actingAs($user)->get('/tickets');
    
    $response->assertStatus(200)
             ->assertSee('My Tickets')
             ->assertSee('Test Ticket');
});

test('tickets index shows only user tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'My Ticket'
    ]);
    
    Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Other Ticket'
    ]);
    
    $response = $this->actingAs($user)->get('/tickets');
    
    $response->assertStatus(200)
             ->assertSee('My Ticket')
             ->assertDontSee('Other Ticket');
});

test('users can view create ticket form', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Technical Support']);
    
    $response = $this->actingAs($user)->get('/tickets/create');
    
    $response->assertStatus(200)
             ->assertSee('Create New Ticket')
             ->assertSee('Technical Support');
});

test('users can create a ticket via web', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $this->actingAs($user)
        ->from('/tickets/create')
        ->post('/tickets', [
            'category_id' => $category->id,
            'title' => 'New Test Ticket',
            'description' => 'This is a test ticket description'
        ]);
    
    $this->assertDatabaseHas('tickets', [
        'user_id' => $user->id,
        'title' => 'New Test Ticket',
        'priority' => 'medium'
    ]);
});

test('ticket creation requires valid fields', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/tickets', [
        'title' => '', // Empty title
        'description' => ''
    ]);
    
    $response->assertSessionHasErrors(['category_id', 'title', 'description']);
});

test('users can view their own ticket', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Billing']);
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'View This Ticket',
        'description' => 'Ticket description text'
    ]);
    
    $response = $this->actingAs($user)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('View This Ticket')
             ->assertSee('Ticket description text')
             ->assertSee('Billing');
});

test('users cannot view other users tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id
    ]);
    
    $response = $this->actingAs($user)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(403);
});

test('users can view edit form for their ticket', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id,
        'title' => 'Edit Me'
    ]);
    
    $response = $this->actingAs($agent)->get("/tickets/{$ticket->id}/edit");
    
    $response->assertStatus(200)
             ->assertSee('Edit Ticket')
             ->assertSee('Edit Me')
             ->assertSee('Support');
});

test('users can update their ticket', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id,
        'title' => 'Original Title',
        'status' => 'open'
    ]);
    
    $this->actingAs($agent)
        ->from("/tickets/{$ticket->id}/edit")
        ->put("/tickets/{$ticket->id}", [
            'category_id' => $category->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'medium',
            'status' => 'in_progress'
        ]);
    
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'title' => 'Updated Title',
        'status' => 'in_progress'
    ]);
});

test('users cannot update other users tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id
    ]);
    
    $response = $this->actingAs($user)->put("/tickets/{$ticket->id}", [
        'category_id' => $category->id,
        'title' => 'Hacked',
        'description' => 'Hacked',
        'status' => 'closed'
    ]);
    
    $response->assertStatus(403);
});

test('users can delete their tickets', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id
    ]);
    
    $this->actingAs($agent)
        ->from("/tickets/{$ticket->id}")
        ->delete("/tickets/{$ticket->id}");
    
    $this->assertDatabaseMissing('tickets', [
        'id' => $ticket->id
    ]);
});

test('users can filter tickets by status', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Open Ticket',
        'status' => 'open'
    ]);
    
    Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Resolved Ticket',
        'status' => 'resolved'
    ]);
    
    $response = $this->actingAs($user)->get('/tickets?status=open');
    
    $response->assertStatus(200)
             ->assertSee('Open Ticket')
             ->assertDontSee('Resolved Ticket');
});

test('ticket show page displays status badge', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id,
        'status' => 'in_progress',
        'priority' => 'high'
    ]);
    
    $response = $this->actingAs($agent)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('In progress')
             ->assertSee('High');
});
