<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>{{ config('app.name', 'Food Delivery Dashboard') }}</title>
    <link rel="icon" href="https://jaramarket.com.ng/assets/img/logo-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <div class="flex h-screen overflow-hidden">
            @include('layouts.sidebar')

            <div class="flex flex-col flex-1 w-0 overflow-hidden">
                @include('layouts.navigation')

                <main class="relative flex-1 overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="px-4 mx-auto max-w-7xl sm:px-6 md:px-8">
                            <h1 class="text-2xl font-semibold text-gray-900">@yield('header')</h1>
                        </div>
                        <div class="px-4 mx-auto max-w-7xl sm:px-6 md:px-8">
                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    @yield('modals')
    @stack('scripts')
</body>

</html>
