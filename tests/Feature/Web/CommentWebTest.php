<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users can add comments to their tickets', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    $this->actingAs($user)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/comments", [
            'body' => 'This is my comment on the ticket'
        ]);
    
    $this->assertDatabaseHas('comments', [
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'body' => 'This is my comment on the ticket'
    ]);
});

test('comment requires body', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    $response = $this->actingAs($user)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/comments", [
            'body' => ''
        ]);
    
    $response->assertSessionHasErrors('body');
});

test('comment body must be at least 3 characters', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    $response = $this->actingAs($user)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/comments", [
            'body' => 'ab'
        ]);
    
    $response->assertSessionHasErrors('body');
});

test('ticket page displays comments', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    $comment = Comment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'body' => 'This is a test comment'
    ]);
    
    $response = $this->actingAs($user)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('This is a test comment')
             ->assertSee('John Doe');
});

test('agents can create internal comments', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id
    ]);
    
    $this->actingAs($agent)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/comments", [
            'body' => 'Internal agent note',
            'is_internal' => true
        ]);
    
    $this->assertDatabaseHas('comments', [
        'ticket_id' => $ticket->id,
        'body' => 'Internal agent note',
        'is_internal' => true
    ]);
});

test('regular users cannot create internal comments', function () {
    $user = User::factory()->create(['is_agent' => false]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    $this->actingAs($user)
        ->from("/tickets/{$ticket->id}")
        ->post("/tickets/{$ticket->id}/comments", [
            'body' => 'Trying to make internal note',
            'is_internal' => true
        ]);
    
    // Comment should be created but NOT as internal
    $this->assertDatabaseHas('comments', [
        'ticket_id' => $ticket->id,
        'body' => 'Trying to make internal note',
        'is_internal' => false // Should be false despite request
    ]);
});

test('ticket page shows comment count', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id
    ]);
    
    Comment::factory()->count(3)->create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id
    ]);
    
    $response = $this->actingAs($user)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('Comments (3)');
});

test('users cannot comment on other users tickets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id
    ]);
    
    $response = $this->actingAs($user)->post("/tickets/{$ticket->id}/comments", [
        'body' => 'Unauthorized comment'
    ]);
    
    $response->assertStatus(403);
});

test('ticket page shows agent badge on comments', function () {
    $agent = User::factory()->create(['is_agent' => true, 'name' => 'Agent Smith']);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id
    ]);
    
    Comment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'body' => 'Agent response here'
    ]);
    
    $response = $this->actingAs($agent)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('Agent');
});

test('ticket page shows internal badge on internal comments', function () {
    $agent = User::factory()->create(['is_agent' => true]);
    $category = Category::factory()->create();
    
    $ticket = Ticket::factory()->create([
        'user_id' => $agent->id,
        'category_id' => $category->id
    ]);
    
    Comment::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'body' => 'Internal note',
        'is_internal' => true
    ]);
    
    $response = $this->actingAs($agent)->get("/tickets/{$ticket->id}");
    
    $response->assertStatus(200)
             ->assertSee('Internal');
});
