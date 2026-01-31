@extends('layouts.guest')

@section('title', 'Login - Resolve')

@section('content')
<div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
        <i class="fas fa-ticket-alt mr-2"></i>Resolve
    </h1>
    <p class="text-gray-600 dark:text-gray-400">Sign in to your account</p>
</div>

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <x-form.input 
            name="email" 
            type="email"
            label="Email Address" 
            :required="true"
            placeholder="you@example.com" />

        <!-- Password -->
        <x-form.input 
            name="password" 
            type="password"
            label="Password" 
            :required="true"
            placeholder="••••••••" 
            class="mb-6" />

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Remember me</span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-md transition duration-150">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                Sign up
            </a>
        </p>
    </div>
</div>

<div class="mt-6 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        API documentation available at 
        <code class="bg-gray-200 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs">/api/*</code>
    </p>
</div>
@endsection
