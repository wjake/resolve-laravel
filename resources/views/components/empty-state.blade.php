@props(['icon' => 'fa-inbox', 'message', 'action' => null, 'actionText' => null])

<div class="px-6 py-12 text-center">
    <i class="fas {{ $icon }} text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $message }}</h3>
    @if($slot->isNotEmpty())
        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $slot }}</p>
    @endif
    @if($action && $actionText)
        <a href="{{ $action }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>
            {{ $actionText }}
        </a>
    @endif
</div>
