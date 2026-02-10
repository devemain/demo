{{--
 | 2026 DeveMain
 |
 | All rights reserved. For internal use only.
 | Unauthorized copying, modification, or distribution is prohibited.
 |
 | @author    DeveMain <devemain@gmail.com>
 | @copyright 2026 DeveMain
 | @license   PROPRIETARY
 |
 | @link      https://github.com/DeveMain
 --}}

@if($version !== '1.0.0')
    <span {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 ml-3 w-fit']) }}>
        <i class="fab fa-git-alt"></i> v{{ $version }}
    </span>
@endif
