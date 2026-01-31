@props(['name', 'label', 'required' => false, 'options' => [], 'selected' => ''])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        @if($required) required @endif
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error($name) border-red-500 @enderror">
        {{ $slot }}
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
