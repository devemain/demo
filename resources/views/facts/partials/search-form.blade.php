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

<form action="{{ route('facts.search') }}" method="GET" class="flex">
    <div class="relative">
        <input type="text" name="q" placeholder="Search facts..."
               class="px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-48 sm:w-64 pr-10"
               value="{{ request('q') }}">
        @if(request('q'))
            <a href="{{ route('facts.index') }}"
               class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 no-transform">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600 transition-colors no-transform">
        <i class="fas fa-search"></i>
    </button>
</form>
