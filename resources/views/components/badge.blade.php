@props(['type' => 'default', 'icon' => null, 'animate' => false])

@php
$classes = match($type) {
    'category' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'priority-high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'priority-medium' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'priority-low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'status-open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'status-progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'status-resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'status-closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'customer-reply' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    'unassigned' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'agent' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'internal' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
};
$animateClass = $animate ? 'animate-pulse' : '';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$classes} {$animateClass}"]) }}>
    @if($icon)
        <i class="{{ $icon }} mr-1"></i>
    @endif
    {{ $slot }}
</span>
