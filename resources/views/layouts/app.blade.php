<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Klinik Makmur Jaya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div
        id="app-shell"
        class="app-shell @auth is-authenticated @else is-guest @endauth"
        data-sidebar-storage-key="kmj.sidebar.collapsed"
        @auth
            data-notification-unread-url="{{ route('notifications.unread') }}"
            data-notification-latest-url="{{ route('notifications.latest') }}"
        @endauth
    >
        @include('layouts.partials.navbar')

        @auth
            <button class="sidebar-overlay" type="button" data-sidebar-close aria-label="Tutup sidebar"></button>
            @include('layouts.partials.sidebar')
        @endauth

        <div class="shell-main-wrap @guest shell-main-wrap-guest @endguest">
            <main id="main-content" class="shell-main" tabindex="-1">
                @include('layouts.partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
