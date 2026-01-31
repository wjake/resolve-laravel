@props(['name', 'label', 'required' => false, 'value' => '', 'placeholder' => '', 'rows' => 4])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        rows="{{ $rows }}"
        @if($required) required @endif
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error($name) border-red-500 @enderror"
        placeholder="{{ $placeholder }}">{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @if($slot->isNotEmpty())
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $slot }}
        </p>
    @endif
</div>
