<a href="{{ route('web.tickets.show', $ticket) }}" class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition relative">
    <div class="flex-1 px-6 py-5">
        <div class="flex items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                {{ $ticket->title }}
            </h3>
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            <i class="far fa-clock mr-1"></i>
            {{ $ticket->created_at->format('M j, Y') }} • {{ $ticket->created_at->diffForHumans() }}
        </p>
        <div class="mt-2 flex items-center text-sm text-gray-500 flex-wrap gap-2">
            <x-badge type="category" icon="fas fa-tag">
                {{ $ticket->category->name }}
            </x-badge>
            @if($ticket->priority)
                <x-badge type="priority-{{ $ticket->priority }}">
                    {{ ucfirst($ticket->priority) }}
                </x-badge>
            @endif
            @if(Auth::user()->is_agent && $ticket->comments->isNotEmpty() && !$ticket->comments->first()->user->is_agent)
                <x-badge type="customer-reply" icon="fas fa-reply" :animate="true">
                    Customer Reply
                </x-badge>
            @endif
            @if(Auth::user()->is_agent && !$ticket->assigned_to && $ticket->status === 'open')
                <x-badge type="unassigned" icon="fas fa-user-clock">
                    Unassigned
                </x-badge>
            @endif
        </div>
    </div>
    <div class="px-6 py-5 flex items-center gap-3 relative z-10">
        @if($ticket->assigned_to)
            @if($ticket->status === 'resolved')
                @if($ticket->assigned_to === Auth::id())
                    <button disabled class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 cursor-not-allowed opacity-75 pointer-events-none">
                        <i class="fas fa-check-circle mr-1"></i>
                        Resolved by You
                    </button>
                @else
                    <button disabled class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 cursor-not-allowed opacity-75 pointer-events-none">
                        <i class="fas fa-check-circle mr-1"></i>
                        Resolved by {{ $ticket->assignedAgent->name }}
                    </button>
                @endif
            @else
                @if($ticket->assigned_to === Auth::id())
                    <button disabled class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 cursor-not-allowed opacity-75 pointer-events-none">
                        <i class="fas fa-tasks mr-1"></i>
                        In Progress
                    </button>
                @else
                    <button disabled class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed opacity-75 pointer-events-none">
                        <i class="fas fa-user mr-1"></i>
                        Assigned to {{ $ticket->assignedAgent->name }}
                    </button>
                @endif
            @endif
        @elseif(Auth::user()->is_agent)
            <form action="{{ route('web.tickets.assign', $ticket) }}" method="POST" class="inline-block" onclick="event.stopPropagation();">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-green-600 text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-user-plus mr-1"></i>
                    Assign to Me
                </button>
            </form>
        @else
            <button disabled class="inline-flex items-center px-3 py-2 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 cursor-not-allowed opacity-75 pointer-events-none">
                <i class="fas fa-circle mr-1"></i>
                Open
            </button>
        @endif
        <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
    </div>
</a>
