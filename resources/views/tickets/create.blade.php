@extends('layouts.app')

@section('title', 'Create Ticket - Resolve')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('web.tickets.index') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Tickets
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Ticket</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill out the form below to submit a support ticket</p>
        </div>

        <form method="POST" action="{{ route('web.tickets.store') }}" class="p-6">
            @csrf

            @if(Auth::user()->is_agent)
                <!-- User Selection for Agents -->
                <div class="mb-6">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Create Ticket For <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                        <option value="">Select a user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Select the user for whom you are creating this ticket.
                    </p>
                </div>
            @endif

            <!-- Category -->
            <x-form.select name="category_id" label="Category" :required="true">
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-form.select>

            <!-- Title -->
            <x-form.input 
                name="title" 
                label="Title" 
                :required="true" 
                maxlength="255"
                placeholder="Brief description of your issue" />

            <!-- Description -->
            <x-form.textarea 
                name="description" 
                label="Description" 
                :required="true"
                rows="6"
                placeholder="Provide detailed information about your issue...">
                Be as specific as possible. Include any relevant details, error messages, or steps to reproduce the issue.
            </x-form.textarea>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('web.tickets.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium inline-flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
