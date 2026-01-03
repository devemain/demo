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

@if($facts->isEmpty())
    @include('facts.partials.no-found-block')
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($facts as $fact)
            @include('facts.partials.fact-frame')
        @endforeach
    </div>

    <div class="mt-8">
        {{ $facts->withQueryString()->links() }}
    </div>
@endif
