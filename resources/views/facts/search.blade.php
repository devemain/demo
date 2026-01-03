{{--
 | 2026 DeveMain
 |
 | All rights reserved. For internal use only.
 | Unauthorized copying, modification, or distribution is prohibited.
 |
 | @author    DeveMain <devemain@gmail.com>
 | @copyright 2026 DeveMain
 | @license   PROPRIETARY
 | @link      https://github.com/DeveMain
 --}}

@extends('_layouts.app')

@section('title', 'Facts search')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Search Results</h1>
    <p class="text-gray-600 mb-4">Found {{ $facts->total() }} results for "{{ $query }}"</p>

    <div class="flex flex-col items-center space-x-4 mb-6">
        @include('facts.partials.search-form')
    </div>

    @include('facts.partials.facts-list')
@endsection
