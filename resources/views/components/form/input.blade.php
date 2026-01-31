@props(['name', 'label', 'type' => 'text', 'required' => false, 'value' => '', 'placeholder' => ''])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}" 
        @if($required) required @endif
        @if($attributes->has('maxlength')) maxlength="{{ $attributes->get('maxlength') }}" @endif
        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error($name) border-red-500 @enderror"
        placeholder="{{ $placeholder }}">
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
