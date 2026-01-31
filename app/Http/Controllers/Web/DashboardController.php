<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Agents see all tickets, regular users see only their own
        if ($user->is_agent) {
            $ticketQuery = \App\Models\Ticket::query();
            
            // Agent-specific stats (their assigned tickets)
            $agentTicketQuery = \App\Models\Ticket::where('assigned_to', $user->id);
            
            $stats = [
                'total_tickets' => (clone $agentTicketQuery)->count(),
                'open_tickets' => (clone $agentTicketQuery)->where('status', 'open')->count(),
                'in_progress_tickets' => (clone $agentTicketQuery)->where('status', 'in_progress')->count(),
                'resolved_tickets' => (clone $agentTicketQuery)->where('status', 'resolved')->count(),
                'assigned_to_me' => (clone $agentTicketQuery)->whereIn('status', ['open', 'in_progress'])->count(),
            ];
        } else {
            $ticketQuery = $user->tickets();
            
            $stats = [
                'total_tickets' => (clone $ticketQuery)->count(),
                'open_tickets' => (clone $ticketQuery)->where('status', 'open')->count(),
                'in_progress_tickets' => (clone $ticketQuery)->where('status', 'in_progress')->count(),
                'resolved_tickets' => (clone $ticketQuery)->where('status', 'resolved')->count(),
            ];
        }

        $recent_tickets = $ticketQuery
            ->with([
                'category', 
                'assignedAgent',
                'comments' => function ($query) {
                    $query->with('user')->latest()->limit(1);
                }
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_tickets'));
    }
}
