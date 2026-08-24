<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAP') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <style>
            /* =============================================
               SIAP - Typography & Design System
               ============================================= */
            :root {
                --font-main: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            }
            body, html {
                font-family: var(--font-main) !important;
                font-size: 14px;
                line-height: 1.6;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            /* Headings */
            h1, h2, h3, h4, h5, h6 {
                font-family: var(--font-main) !important;
                font-weight: 600;
                letter-spacing: -0.01em;
            }
            /* Tables standardization */
            th {
                font-family: var(--font-main) !important;
                font-size: 0.7rem;
                font-weight: 600;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: #6b7280;
            }
            td {
                font-family: var(--font-main) !important;
                font-size: 0.8rem;
            }
            /* Nav/sidebar */
            nav, aside {
                font-family: var(--font-main) !important;
            }
            /* Buttons */
            button, a.btn, [role="button"] {
                font-family: var(--font-main) !important;
            }
            /* Form inputs */
            input, select, textarea {
                font-family: var(--font-main) !important;
                font-size: 0.875rem;
            }
        </style>

        <!-- TomSelect -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">

            <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
                 class="fixed inset-0 top-10 bg-black/30 z-30 lg:hidden"></div>

            <x-sidebar />

            <div class="transition-all duration-200" :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white border-b border-gray-100">
                        <div class="py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- TomSelect JS + Auto Init -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('select.ts-select').forEach(function (el) {
                    new TomSelect(el, {
                        placeholder: el.dataset.placeholder || '-- Pilih --',
                        allowEmptyOption: true,
                        maxOptions: null,
                    });
                });
            });
        </script>
    </body>
</html>