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

@section('title', 'Statistics')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Statistics of Facts</h1>
    <p class="text-gray-600 mb-4">Overview of facts database metrics</p>

    <div class="flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 max-w-6xl">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Facts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($total) }}</p>
                        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Added Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $today }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($yesterday > 0)
                                vs {{ $yesterday }} yesterday
                                @if($today > $yesterday)
                                    <span class="text-green-600">↑ {{ round(($today/$yesterday - 1)*100) }}%</span>
                                @elseif($today < $yesterday)
                                    <span class="text-red-600">↓ {{ round((1 - $today/$yesterday)*100) }}%</span>
                                @endif
                            @else&nbsp;@endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $thisMonth }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ now()->format('F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($recent->isEmpty())
        @include('facts.partials.no-found-block')
    @else
        <div class="flex justify-center">
            <div class="max-w-3xl w-full">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recently Added</h3>
                    <div class="space-y-3">
                        @foreach($recent as $fact)
                            <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                <p class="text-sm text-gray-800 line-clamp-2 text-justify">{{ $fact->content }}</p>
                                <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">
                                    {{ $fact->created_at->diffForHumans() }}
                                </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
