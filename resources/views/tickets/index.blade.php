@extends('layouts.app')

@section('title', (Auth::user()->is_agent ? 'Tickets' : 'My Tickets') . ' - Resolve')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ Auth::user()->is_agent ? 'Tickets' : 'My Tickets' }}</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">{{ Auth::user()->is_agent ? 'Manage all support tickets' : 'Manage your support tickets' }}</p>
    </div>
    <a href="{{ route('web.tickets.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium inline-flex items-center self-start sm:self-auto">
        <i class="fas fa-plus mr-2"></i>
        New Ticket
    </a>
</div>

<!-- Filter Tabs -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <nav class="flex -mb-px overflow-x-auto sm:overflow-visible">
                <a href="{{ route('web.tickets.index', request()->only('assigned', 'category')) }}" class="@if(!request('status')) border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    All
                </a>
                <a href="{{ route('web.tickets.index', array_merge(['status' => 'open'], request()->only('assigned', 'category'))) }}" class="@if(request('status') === 'open') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Open
                </a>
                <a href="{{ route('web.tickets.index', array_merge(['status' => 'in_progress'], request()->only('assigned', 'category'))) }}" class="@if(request('status') === 'in_progress') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    In Progress
                </a>
                <a href="{{ route('web.tickets.index', array_merge(['status' => 'resolved'], request()->only('assigned', 'category'))) }}" class="@if(request('status') === 'resolved') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Resolved
                </a>
                <a href="{{ route('web.tickets.index', array_merge(['status' => 'closed'], request()->only('assigned', 'category'))) }}" class="@if(request('status') === 'closed') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 @endif whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Closed
                </a>
            </nav>
            <div class="px-6 py-4 sm:hidden">
                <button type="button" onclick="toggleFiltersPanel()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-sliders-h mr-2"></i>
                    Filters
                </button>
            </div>
            <div id="filters-panel" class="hidden sm:flex flex-col sm:flex-row sm:items-center gap-4 px-6 py-4">
                <div class="flex items-center">
                    <label for="category-filter" class="text-sm text-gray-700 dark:text-gray-300 mr-2">
                        <i class="fas fa-tag mr-1"></i>
                        Category:
                    </label>
                    <select id="category-filter" 
                            class="w-full sm:w-auto rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 {{ request('category') ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-medium' : 'border-gray-300 dark:border-gray-600' }}"
                            onchange="
                                const params = new URLSearchParams(window.location.search);
                                if (this.value) {
                                    params.set('category', this.value);
                                } else {
                                    params.delete('category');
                                }
                                window.location.href = '{{ route('web.tickets.index') }}' + '?' + params.toString();
                            ">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(Auth::user()->is_agent)
                    <div class="flex items-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" 
                                   {{ request('assigned') === 'me' ? 'checked' : '' }}
                                   onchange="window.location.href = this.checked ? '{{ route('web.tickets.index', array_merge(request()->only('status', 'category'), ['assigned' => 'me'])) }}' : '{{ route('web.tickets.index', request()->only('status', 'category')) }}'">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user-check mr-1"></i>
                                Assigned to Me
                            </span>
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tickets List -->
<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    @forelse($tickets as $ticket)
        <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
            @include('tickets._ticket-list-item')
        </div>
    @empty
        <x-empty-state 
            icon="fa-inbox" 
            message="No tickets found"
            :action="route('web.tickets.create')"
            actionText="Create Ticket">
            Get started by creating your first support ticket.
        </x-empty-state>
    @endforelse
</div>

<!-- Pagination -->
@if($tickets->hasPages())
    <div class="mt-6">
        {{ $tickets->appends(request()->query())->links() }}
    </div>
@endif

<script>
    function toggleFiltersPanel() {
        const panel = document.getElementById('filters-panel');
        if (!panel) return;
        panel.classList.toggle('hidden');
    }
</script>
@endsection
