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

<div class="flex flex-col fact-card bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
    <div class="flex justify-between items-start mb-3">
        <span class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded">
            {{ $fact->created_at->format('M d, Y') }}
        </span>
    </div>

    <p class="text-gray-800 mb-4 text-justify">{{ $fact->content }}</p>

    <div class="pt-3 border-t border-gray-100 flex justify-between text-sm text-gray-500 mt-auto">
        <div class="flex items-center">
            <i class="fas fa-hashtag mr-1 text-blue-500"></i>
            <span class="font-medium">ID: {{ $fact->id }}</span>
        </div>
        <div class="flex items-center">
            <i class="far fa-clock mr-1 text-gray-400"></i>
            <span>{{ $fact->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
