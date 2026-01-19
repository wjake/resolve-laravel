<?php

test('authenticated users can create a ticket', function () {
    $user = \App\Models\User::factory()->create();
    $category = \App\Models\Category::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/tickets', [
            'category_id' => $category->id,
            'title' => 'My first ticket',
            'description' => 'Something is broken!',
            'priority' => 'high'
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('data.title', 'My first ticket');
});

test('users can see only their own tickets', function () {
    $user = \App\Models\User::factory()->create();
    $otherUser = \App\Models\User::factory()->create();
    $category = \App\Models\Category::factory()->create();

    // Create a ticket for our user
    \App\Models\Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'My Ticket'
    ]);

    // Create a ticket for someone else
    \App\Models\Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Someone elses Ticket'
    ]);

    $response = $this->actingAs($user)->getJson('/api/tickets');

    $response->assertStatus(200)
             ->assertJsonCount(1, 'data') // Should only see 1 ticket
             ->assertJsonPath('data.0.title', 'My Ticket');
});

test('users cannot view tickets belonging to others', function () {
    $user = \App\Models\User::factory()->create();
    $otherUser = \App\Models\User::factory()->create();
    
    $ticket = \App\Models\Ticket::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/tickets/{$ticket->id}");

    $response->assertStatus(403);
});

test('users can update their own tickets', function () {
    $user = \App\Models\User::factory()->create();
    $ticket = \App\Models\Ticket::factory()->create([
        'user_id' => $user->id,
        'title' => 'Old Title'
    ]);

    $response = $this->actingAs($user)->patchJson("/api/tickets/{$ticket->id}", [
        'title' => 'New Updated Title'
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'title' => 'New Updated Title'
    ]);
});

test('user can comment on their own ticket', function () {
    $user = \App\Models\User::factory()->create();
    $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/tickets/{$ticket->id}/comments", [
            'body' => 'This is a test comment'
        ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('comments', [
        'body' => 'This is a test comment',
        'ticket_id' => $ticket->id
    ]);
});
