<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Resolve - Support Ticket System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Check for saved theme preference or default to light mode
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            <i class="fas fa-ticket-alt mr-2"></i>Resolve
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) border-indigo-500 text-gray-900 dark:text-white @else border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-100 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('web.tickets.index') }}" class="@if(request()->routeIs('web.tickets.index') || request()->routeIs('web.tickets.show')) border-indigo-500 text-gray-900 dark:text-white @else border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-100 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            @if(Auth::user()->is_agent)
                                Tickets
                            @else
                                My Tickets
                            @endif
                        </a>
                        <a href="{{ route('web.tickets.create') }}" class="@if(request()->routeIs('web.tickets.create') || request()->routeIs('web.tickets.edit')) border-indigo-500 text-gray-900 dark:text-white @else border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-100 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            New Ticket
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Mobile Menu Toggle -->
                    <button onclick="toggleMobileMenu()" class="sm:hidden p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    <span class="hidden sm:inline text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 sm:px-4 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="sm:hidden hidden border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium @if(request()->routeIs('dashboard')) text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-gray-700 @else text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 @endif">
                    Dashboard
                </a>
                <a href="{{ route('web.tickets.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium @if(request()->routeIs('web.tickets.index') || request()->routeIs('web.tickets.show')) text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-gray-700 @else text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 @endif">
                    @if(Auth::user()->is_agent)
                        Tickets
                    @else
                        My Tickets
                    @endif
                </a>
                <a href="{{ route('web.tickets.create') }}" class="block px-3 py-2 rounded-md text-sm font-medium @if(request()->routeIs('web.tickets.create') || request()->routeIs('web.tickets.edit')) text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-gray-700 @else text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 @endif">
                    New Ticket
                </a>
                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    Signed in as {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            if (!menu) return;
            menu.classList.toggle('hidden');
        }
    </script>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 dark:text-gray-400 text-sm">
                &copy; {{ date('Y') }} Resolve Support System. API Available at <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">/api/*</code>
            </p>
        </div>
    </footer>
</body>
</html>
</html>
