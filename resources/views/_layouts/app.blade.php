{{--
 | 2025 DeveMain
 |
 | All rights reserved. For internal use only.
 | Unauthorized copying, modification, or distribution is prohibited.
 |
 | @author    DeveMain <devemain@gmail.com>
 | @copyright 2025 DeveMain
 | @license   PROPRIETARY
 | @link      https://github.com/DeveMain
 --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($__env->yieldContent('title'))
        <title>@yield('title') | DeveMain</title>
    @else
        <title>Demo Project | DeveMain</title>
    @endif
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="no-transform">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo h-20">
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ url('/') }}" class="px-3 py-2 {{ request()->is('/') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="{{ route('facts.index') }}" class="px-3 py-2 {{ request()->routeIs('facts.index') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-rocket"></i> Tech Facts
                </a>
                <a href="{{ route('facts.stats') }}" class="px-3 py-2 {{ request()->routeIs('facts.stats') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-chart-line"></i> Statistics
                </a>
            </div>
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden hidden py-4 border-t">
            <div class="flex flex-col space-y-3">
                <a href="{{ url('/') }}" class="px-3 py-2 {{ request()->is('/') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="{{ route('facts.index') }}" class="px-3 py-2 {{ request()->routeIs('facts.index') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-list mr-2"></i>Tech Facts
                </a>
                <a href="{{ route('facts.stats') }}" class="px-3 py-2 {{ request()->routeIs('facts.stats') ? 'active-link' : 'text-gray-700 hover:text-blue-800' }}">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center">
    @yield('content')
</main>

<footer class="bg-white border-t py-6">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
        <p class="text-sm md:text-base">
            Sergey Alekseev | PHP Web Developer | <a href="mailto:devemain@gmail.com">devemain@gmail.com</a>
        </p>
    </div>
</footer>
</body>
</html>
