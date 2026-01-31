@extends('layouts.guest')

@section('title', 'Register - Resolve')

@section('content')
<div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
        <i class="fas fa-ticket-alt mr-2"></i>Resolve
    </h1>
    <p class="text-gray-600 dark:text-gray-400">Create your account</p>
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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <x-form.input 
            name="name" 
            label="Full Name" 
            :required="true"
            placeholder="John Doe" />

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
            placeholder="••••••••" />

        <!-- Confirm Password -->
        <x-form.input 
            name="password_confirmation" 
            type="password"
            label="Confirm Password" 
            :required="true"
            placeholder="••••••••" 
            class="mb-6" />

        <!-- Submit -->
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-md transition duration-150">
            Create Account
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                Sign in
            </a>
        </p>
    </div>
</div>
@endsection
