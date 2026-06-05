<header class="app-navbar">
    <div class="app-navbar-inner">
        <div class="navbar-brand-group">
            @auth
                <button
                    class="icon-button"
                    type="button"
                    data-sidebar-toggle
                    aria-controls="app-sidebar"
                    aria-expanded="false"
                    aria-label="Toggle sidebar"
                    title="Toggle sidebar"
                >
                    <span class="icon-lines" aria-hidden="true"></span>
                </button>
            @endauth

            <a href="{{ url('/') }}" class="app-brand">
                <span class="brand-mark">KMJ</span>
                <span class="brand-text">Klinik Makmur Jaya</span>
            </a>
        </div>

        <nav class="navbar-links" aria-label="Navigasi utama">
            <a class="nav-link" href="{{ route('catalog.index') }}">Katalog</a>

            @auth
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link nav-notification" href="{{ route('notifications.index') }}">
                    Notifikasi
                    <span id="notification-count" class="badge hidden">0</span>
                </a>
                <span class="role-chip">{{ auth()->user()->role }}</span>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-muted" type="submit">Logout</button>
                </form>
            @else
                <a class="btn btn-muted" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </div>
</header>
