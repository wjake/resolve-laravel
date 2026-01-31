@extends('layouts.app')

@section('title', 'Dashboard - Resolve')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
    <p class="mt-2 text-gray-600 dark:text-gray-300">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-{{ Auth::user()->is_agent ? '5' : '4' }} gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-ticket-alt text-3xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            @if(Auth::user()->is_agent)
                                My Total Tickets
                            @else
                                Total Tickets
                            @endif
                        </dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-folder-open text-3xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            @if(Auth::user()->is_agent)
                                My Open
                            @else
                                Open
                            @endif
                        </dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['open_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-spinner text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            @if(Auth::user()->is_agent)
                                My In Progress
                            @else
                                In Progress
                            @endif
                        </dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['in_progress_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            @if(Auth::user()->is_agent)
                                My Resolved
                            @else
                                Resolved
                            @endif
                        </dt>
                        <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['resolved_tickets'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->is_agent)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-check text-3xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active (Open + In Progress)</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['assigned_to_me'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Recent Tickets -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Tickets</h2>
    </div>
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($recent_tickets as $ticket)
            @include('tickets._ticket-list-item')
        @empty
            <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>No tickets yet. <a href="{{ route('web.tickets.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Create your first ticket</a></p>
            </div>
        @endforelse
    </div>
    @if($recent_tickets->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('web.tickets.index') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                View all tickets <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    @endif
</div>
@endsection
