@extends('layouts.app')

@section('title', $ticket->title . ' - Resolve')

@section('content')
<div class="mb-6">
    <a href="{{ route('web.tickets.index') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Tickets
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Ticket Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $ticket->title }}</h1>
                        <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center">
                                <i class="far fa-clock mr-1"></i>
                                Created {{ $ticket->created_at->diffForHumans() }}
                            </span>
                            @if($ticket->updated_at != $ticket->created_at)
                                <span class="inline-flex items-center">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    Updated {{ $ticket->updated_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @can('update', $ticket)
                    <div class="ml-4">
                        <a href="{{ route('web.tickets.edit', $ticket) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                    </div>
                    @endcan
                </div>

                <div class="prose max-w-none">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="far fa-comments mr-2"></i>
                    Comments ({{ $ticket->comments->count() }})
                </h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($ticket->comments as $comment)
                    <div class="px-6 py-5 @if($comment->is_internal) bg-blue-50 dark:bg-blue-900/20 @endif">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                        @if($comment->user->is_agent)
                                            <x-badge type="agent" icon="fas fa-shield-alt" class="ml-2">
                                                Agent
                                            </x-badge>
                                        @endif
                                        @if($comment->is_internal)
                                            <x-badge type="internal" icon="fas fa-lock" class="ml-2">
                                                Internal
                                            </x-badge>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $comment->body }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="far fa-comment text-4xl mb-3"></i>
                        <p>No comments yet. Be the first to comment!</p>
                    </div>
                @endforelse
            </div>

            <!-- Add Comment Form -->
            <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('web.tickets.comments.store', $ticket) }}">
                    @csrf
                    <x-form.textarea 
                        name="body" 
                        label="Add a comment" 
                        :required="true"
                        rows="4"
                        placeholder="Write your comment here..." />

                    @if(Auth::user()->is_agent)
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-lock mr-1"></i>
                                    Internal note (only visible to agents)
                                </span>
                            </label>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium inline-flex items-center">
                            <i class="far fa-paper-plane mr-2"></i>
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ticket Details</h3>
            
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <x-badge :type="'status-' . ($ticket->status === 'in_progress' ? 'progress' : $ticket->status)">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </x-badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                    <dd class="mt-1">
                        <x-badge type="category" icon="fas fa-tag">
                            {{ $ticket->category->name }}
                        </x-badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned To</dt>
                    <dd class="mt-1">
                        @if($ticket->assigned_to)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-user-check mr-1"></i>
                                {{ $ticket->assignedAgent->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <i class="fas fa-circle mr-1"></i>
                                Unassigned
                            </span>
                        @endif
                    </dd>
                </div>

                @auth
                    @if(Auth::user()->is_agent && $ticket->priority)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority</dt>
                            <dd class="mt-1">
                                <x-badge :type="'priority-' . $ticket->priority">
                                    {{ ucfirst($ticket->priority) }}
                                </x-badge>
                            </dd>
                        </div>
                    @endif
                @endauth

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ticket ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 font-mono">#{{ $ticket->id }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $ticket->created_at->format('M d, Y g:i A') }}</dd>
                </div>

                @if($ticket->updated_at != $ticket->created_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $ticket->updated_at->format('M d, Y g:i A') }}</dd>
                    </div>
                @endif
            </dl>

            @can('assign', $ticket)
            <div class="mt-4">
                <form method="POST" action="{{ route('web.tickets.assign', $ticket) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium inline-flex items-center justify-center">
                        <i class="fas fa-hand-paper mr-2"></i>
                        Assign to Me
                    </button>
                </form>
            </div>
            @endcan

            @can('delete', $ticket)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('web.tickets.destroy', $ticket) }}" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium inline-flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Ticket
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
