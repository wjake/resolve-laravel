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
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $ticket = \App\Models\Ticket::factory()->create([
        'user_id' => $agent->id,
        'title' => 'Old Title'
    ]);

    $response = $this->actingAs($agent)->patchJson("/api/tickets/{$ticket->id}", [
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

test('regular users cannot post internal comments', function () {
    $user = \App\Models\User::factory()->create(['is_agent' => false]);
    $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/tickets/{$ticket->id}/comments", [
            'body' => 'I am trying to be sneaky',
            'is_internal' => true
        ]);

    // The comment should be created, but is_internal MUST be false
    $this->assertDatabaseHas('comments', [
        'body' => 'I am trying to be sneaky',
        'is_internal' => false
    ]);
});
test('agents can assign unassigned tickets to themselves', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $user = \App\Models\User::factory()->create(['is_agent' => false]);
    $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => null]);

    $response = $this->actingAs($agent)
        ->post(route('web.tickets.assign', $ticket));

    $response->assertRedirect(route('web.tickets.show', $ticket));
    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'assigned_to' => $agent->id
    ]);
});

test('agents cannot assign already assigned tickets', function () {
    $agent1 = \App\Models\User::factory()->create(['is_agent' => true]);
    $agent2 = \App\Models\User::factory()->create(['is_agent' => true]);
    $user = \App\Models\User::factory()->create(['is_agent' => false]);
    $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => $agent1->id]);

    $response = $this->actingAs($agent2)
        ->post(route('web.tickets.assign', $ticket));

    $response->assertStatus(403);
});

test('regular users cannot assign tickets', function () {
    $user = \App\Models\User::factory()->create(['is_agent' => false]);
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => null]);

    $response = $this->actingAs($user)
        ->post(route('web.tickets.assign', $ticket));

    $response->assertStatus(403);
});

test('agents can filter tickets by category', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $category1 = \App\Models\Category::factory()->create(['name' => 'Billing']);
    $category2 = \App\Models\Category::factory()->create(['name' => 'Technical']);

    // Create tickets in different categories
    \App\Models\Ticket::factory()->create(['category_id' => $category1->id, 'title' => 'Billing Issue']);
    \App\Models\Ticket::factory()->create(['category_id' => $category1->id, 'title' => 'Payment Problem']);
    \App\Models\Ticket::factory()->create(['category_id' => $category2->id, 'title' => 'Technical Issue']);

    $response = $this->actingAs($agent)
        ->get(route('web.tickets.index', ['category' => $category1->id]));

    $response->assertStatus(200);
    $response->assertSee('Billing Issue');
    $response->assertSee('Payment Problem');
    $response->assertDontSee('Technical Issue');
});

test('users can filter their own tickets by category', function () {
    $user = \App\Models\User::factory()->create(['is_agent' => false]);
    $category1 = \App\Models\Category::factory()->create(['name' => 'Billing']);
    $category2 = \App\Models\Category::factory()->create(['name' => 'Technical']);

    // Create tickets for the user
    \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'category_id' => $category1->id, 'title' => 'My Billing Issue']);
    \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'category_id' => $category2->id, 'title' => 'My Technical Issue']);

    $response = $this->actingAs($user)
        ->get(route('web.tickets.index', ['category' => $category1->id]));

    $response->assertStatus(200);
    $response->assertSee('My Billing Issue');
    $response->assertDontSee('My Technical Issue');
});

test('agents can filter assigned to me tickets', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $otherAgent = \App\Models\User::factory()->create(['is_agent' => true]);
    $user = \App\Models\User::factory()->create(['is_agent' => false]);

    // Create tickets with different assignments
    $myTicket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => $agent->id, 'title' => 'Assigned to Me']);
    $otherTicket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => $otherAgent->id, 'title' => 'Assigned to Other']);
    $unassignedTicket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'assigned_to' => null, 'title' => 'Unassigned']);

    $response = $this->actingAs($agent)
        ->get(route('web.tickets.index', ['assigned' => 'me']));

    $response->assertStatus(200);
    $response->assertSee('Assigned to Me');
    $response->assertDontSee('Assigned to Other');
    $response->assertDontSee('Unassigned');
});

test('agents can combine category and assigned filters', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $category1 = \App\Models\Category::factory()->create(['name' => 'Billing']);
    $category2 = \App\Models\Category::factory()->create(['name' => 'Technical']);

    // Create tickets with various combinations
    \App\Models\Ticket::factory()->create(['assigned_to' => $agent->id, 'category_id' => $category1->id, 'title' => 'My Billing']);
    \App\Models\Ticket::factory()->create(['assigned_to' => $agent->id, 'category_id' => $category2->id, 'title' => 'My Technical']);
    \App\Models\Ticket::factory()->create(['assigned_to' => null, 'category_id' => $category1->id, 'title' => 'Other Billing']);

    $response = $this->actingAs($agent)
        ->get(route('web.tickets.index', ['assigned' => 'me', 'category' => $category1->id]));

    $response->assertStatus(200);
    $response->assertSee('My Billing');
    $response->assertDontSee('My Technical');
    $response->assertDontSee('Other Billing');
});

test('category filter dropdown shows all categories', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);
    $category1 = \App\Models\Category::factory()->create(['name' => 'Billing']);
    $category2 = \App\Models\Category::factory()->create(['name' => 'Technical Support']);

    $response = $this->actingAs($agent)
        ->get(route('web.tickets.index'));

    $response->assertStatus(200);
    $response->assertSee('All Categories');
    $response->assertSee('Billing');
    $response->assertSee('Technical Support');
});

test('agents can filter tickets by status', function () {
    $agent = \App\Models\User::factory()->create(['is_agent' => true]);

    \App\Models\Ticket::factory()->create(['status' => 'open', 'title' => 'Open Ticket']);
    \App\Models\Ticket::factory()->create(['status' => 'in_progress', 'title' => 'In Progress Ticket']);
    \App\Models\Ticket::factory()->create(['status' => 'resolved', 'title' => 'Resolved Ticket']);

    $response = $this->actingAs($agent)
        ->get(route('web.tickets.index', ['status' => 'open']));

    $response->assertStatus(200);
    $response->assertSee('Open Ticket');
    $response->assertDontSee('In Progress Ticket');
    $response->assertDontSee('Resolved Ticket');
});