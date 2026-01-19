<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $validated = $request->validate([
            'body' => 'required|string|min:3',
        ]);

        $comment = $ticket->comments()->create([
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
            'is_internal' => false,
        ]);

        return new CommentResource($comment);
    }
}
