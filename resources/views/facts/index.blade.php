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

@extends('_layouts.app')

@section('title', 'Tech Facts')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Tech Facts</h1>
    <p class="text-gray-600 mb-4">Overview of facts database</p>

    @if(!$facts->isEmpty())
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-4 gap-4">
                <p class="text-gray-600 whitespace-nowrap">
                    Showing {{ $facts->firstItem() }} to {{ $facts->lastItem() }} of {{ $totalFacts }} facts
                </p>

                <div class="flex items-center space-x-4">
                    @include('facts.partials.search-form')
                </div>

                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 text-sm sm:text-base whitespace-nowrap">Items per page:</span>
                    <div class="relative">
                        <select onchange="window.location.href='?per_page='+this.value"
                                class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 cursor-pointer shadow-sm">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('facts.partials.facts-list')
@endsection
