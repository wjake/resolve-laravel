<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->is_agent 
            ? Ticket::query() 
            : $request->user()->tickets();
        
        $tickets = $query
            ->with([
                'category', 
                'assignedAgent',
                'comments' => function ($query) {
                    $query->with('user')->latest()->limit(1);
                }
            ])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->category, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($request->assigned === 'me' && $request->user()->is_agent, function ($query) use ($request) {
                return $query->where('assigned_to', $request->user()->id);
            })
            ->latest()
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $users = request()->user()->is_agent 
            ? \App\Models\User::where('is_agent', false)->orderBy('name')->get() 
            : collect();
        
        return view('tickets.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => $request->user()->is_agent ? 'required|exists:users,id' : 'nullable',
        ]);

        $validated['status'] = 'open';
        $validated['priority'] = 'medium';

        if ($request->user()->is_agent) {
            // Agent creating ticket on behalf of user
            $user = \App\Models\User::findOrFail($validated['user_id']);
            
            // Ensure agents can only create tickets for non-agent users
            if ($user->is_agent) {
                return back()->withErrors(['user_id' => 'Cannot create tickets for other agents.'])->withInput();
            }
            
            $user->tickets()->create($validated);
        } else {
            // Regular user creating their own ticket
            $request->user()->tickets()->create($validated);
        }

        return redirect()->route('web.tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['category', 'comments.user']);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $categories = Category::all();
        return view('tickets.edit', compact('ticket', 'categories'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update($validated);

        return redirect()->route('web.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);
        
        $ticket->delete();

        return redirect()->route('web.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function assign(Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        $ticket->update(['assigned_to' => auth()->id()]);

        return redirect()->route('web.tickets.show', $ticket)
            ->with('success', 'Ticket assigned to you successfully!');
    }
}
