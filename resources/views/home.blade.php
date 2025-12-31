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

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-2">
        Demo Project by DeveMain
    </h1>

    <div data-vue-component="FactFetcher" class="max-w-4xl mx-auto my-8"></div>

    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-2">
                    <i class="fas fa-bolt-lightning"></i>
                    Fast
                </h3>
                <p class="text-gray-600">Optimized Laravel application</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-2">
                    <i class="fas fa-gears"></i>
                    Modern
                </h3>
                <p class="text-gray-600">Latest PHP & Laravel features</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-2">
                    <i class="fab fa-docker"></i>
                    Dockerized
                </h3>
                <p class="text-gray-600">Containerized with Docker</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-2">
                    <i class="fas fa-laptop-code"></i>
                    Deployed
                </h3>
                <p class="text-gray-600">Live on Render.com</p>
            </div>
        </div>
    </div>

    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Technology Stack</h2>
        <div class="flex flex-wrap justify-center gap-3 mb-3">
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-medium text-sm">
                <i class="fab fa-php"></i>
                PHP 8.4+
            </span>
            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium text-sm">
                <i class="fab fa-laravel"></i>
                Laravel 12.x
            </span>
            <span class="px-4 py-2 bg-orange-100 text-orange-800 rounded-full font-medium text-sm">
                <i class="fab fa-linux"></i>
                Linux
            </span>
            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-medium text-sm">
                <i class="fas fa-server"></i>
                Nginx
            </span>
            <span class="px-4 py-2 bg-cyan-100 text-cyan-800 rounded-full font-medium text-sm">
                <i class="fas fa-database"></i>
                SQLite
            </span>
            <span class="px-4 py-2 bg-sky-100 text-sky-800 rounded-full font-medium text-sm">
                <i class="fab fa-docker"></i>
                Docker
            </span>
            <span class="px-4 py-2 bg-teal-100 text-teal-800 rounded-full font-medium text-sm">
                <i class="fab fa-github"></i>
                GitHub Actions (CI/CD)
            </span>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
            <span class="px-4 py-2 bg-sky-50 text-sky-800 rounded-full font-medium text-sm">
                <i class="fas fa-box-open"></i>
                Composer
            </span>
            <span class="px-4 py-2 bg-red-50 text-red-800 rounded-full font-medium text-sm">
                <i class="fab fa-npm"></i>
                npm
            </span>
            <span class="px-4 py-2 bg-teal-50 text-teal-800 rounded-full font-medium text-sm">
                <i class="fab fa-vuejs"></i>
                Vue.js
            </span>
            <span class="px-4 py-2 bg-cyan-50 text-cyan-800 rounded-full font-medium text-sm">
                <i class="fab fa-css"></i>
                Tailwind CSS
            </span>
            <span class="px-4 py-2 bg-orange-50 text-orange-800 rounded-full font-medium text-sm">
                <i class="fas fa-bolt-lightning"></i>
                Vite
            </span>
            <span class="px-4 py-2 bg-green-50 text-green-800 rounded-full font-medium text-sm">
                <i class="fas fa-brain"></i>
                AI API
            </span>
        </div>
    </div>

    <div class="mt-12">
        <a href="https://github.com/devemain/demo" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded-lg font-medium transition-colors">
            <i class="fab fa-github"></i>
            View on GitHub
        </a>
    </div>
@endsection
