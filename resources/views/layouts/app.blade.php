<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Klinik Makmur Jaya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
                <a href="{{ url('/') }}" class="font-semibold tracking-wide text-teal-700">Klinik Makmur Jaya</a>
                <nav class="flex items-center gap-3 text-sm">
                    <a class="nav-link" href="{{ route('catalog.index') }}">Katalog</a>
                    @auth
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        <a class="nav-link relative" href="{{ route('notifications.index') }}">
                            Notifikasi
                            <span id="notification-count" class="badge hidden">0</span>
                        </a>
                        <span class="rounded bg-slate-100 px-2 py-1 text-xs uppercase text-slate-600">{{ auth()->user()->role }}</span>
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

        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-6 lg:grid-cols-[220px_1fr]">
            @auth
                <aside class="space-y-2 lg:sticky lg:top-4 lg:h-fit">
                    @php($role = auth()->user()->role)
                    @if($role === 'admin')
                        <a class="side-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <a class="side-link" href="{{ route('admin.categories.index') }}">Kategori</a>
                        <a class="side-link" href="{{ route('admin.suppliers.index') }}">Supplier</a>
                        <a class="side-link" href="{{ route('admin.medicines.index') }}">Obat</a>
                        <a class="side-link" href="{{ route('admin.medicine-batches.index') }}">Batch Stok</a>
                        <a class="side-link" href="{{ route('admin.reports.index') }}">Laporan</a>
                        <a class="side-link" href="{{ route('admin.imports.index') }}">Import CSV</a>
                        <a class="side-link" href="{{ route('admin.monitoring.index') }}">Monitoring</a>
                        <a class="side-link" href="{{ route('admin.error-logs.index') }}">Error Log</a>
                        <a class="side-link" href="{{ route('admin.audit-logs.index') }}">Audit Log</a>
                        <a class="side-link" href="{{ route('admin.simulations.index') }}">Simulasi</a>
                    @elseif($role === 'apoteker')
                        <a class="side-link" href="{{ route('apoteker.dashboard') }}">Dashboard</a>
                        <a class="side-link" href="{{ route('apoteker.prescriptions') }}">Verifikasi Resep</a>
                        <a class="side-link" href="{{ route('apoteker.stock-alerts') }}">Stok & Expired</a>
                    @elseif($role === 'kasir')
                        <a class="side-link" href="{{ route('kasir.dashboard') }}">Dashboard</a>
                        <a class="side-link" href="{{ route('kasir.sales.create') }}">Transaksi Baru</a>
                    @else
                        <a class="side-link" href="{{ route('catalog.index') }}">Katalog</a>
                        <a class="side-link" href="{{ route('cart.index') }}">Cart</a>
                        <a class="side-link" href="{{ route('orders.index') }}">Riwayat Pesanan</a>
                    @endif
                </aside>
            @endauth

            <main class="@guest lg:col-span-2 @endguest">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    @auth
        <script>
            window.notificationEndpoints = {
                unread: "{{ route('notifications.unread') }}",
                latest: "{{ route('notifications.latest') }}",
            };
        </script>
    @endauth
    @stack('scripts')
</body>
</html>
