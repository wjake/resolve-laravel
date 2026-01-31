<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $validated = $request->validate([
            'body' => 'required|string|min:3',
            'is_internal' => 'sometimes|boolean',
        ]);

        $ticket->comments()->create([
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
            'is_internal' => $request->user()->is_agent && ($request->is_internal ?? false),
        ]);

        return redirect()->route('web.tickets.show', $ticket)
            ->with('success', 'Comment added successfully!');
    }
}
