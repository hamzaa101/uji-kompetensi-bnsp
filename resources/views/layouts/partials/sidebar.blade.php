@php
    $role = auth()->user()->role;
    $menus = [
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Kategori', 'route' => 'admin.categories.index'],
            ['label' => 'Supplier', 'route' => 'admin.suppliers.index'],
            ['label' => 'Obat', 'route' => 'admin.medicines.index'],
            ['label' => 'Batch Stok', 'route' => 'admin.medicine-batches.index'],
            ['label' => 'Laporan', 'route' => 'admin.reports.index'],
            ['label' => 'Import CSV', 'route' => 'admin.imports.index'],
            ['label' => 'Monitoring', 'route' => 'admin.monitoring.index'],
            ['label' => 'Error Log', 'route' => 'admin.error-logs.index'],
            ['label' => 'Audit Log', 'route' => 'admin.audit-logs.index'],
            ['label' => 'Simulasi', 'route' => 'admin.simulations.index'],
        ],
        'apoteker' => [
            ['label' => 'Dashboard', 'route' => 'apoteker.dashboard'],
            ['label' => 'Verifikasi Resep', 'route' => 'apoteker.prescriptions'],
            ['label' => 'Stok & Expired', 'route' => 'apoteker.stock-alerts'],
        ],
        'kasir' => [
            ['label' => 'Dashboard', 'route' => 'kasir.dashboard'],
            ['label' => 'Transaksi Baru', 'route' => 'kasir.sales.create'],
        ],
        'pasien' => [
            ['label' => 'Katalog', 'route' => 'catalog.index'],
            ['label' => 'Cart', 'route' => 'cart.index'],
            ['label' => 'Riwayat Pesanan', 'route' => 'orders.index'],
        ],
    ];

    $items = $menus[$role] ?? $menus['pasien'];
@endphp

<aside id="app-sidebar" class="app-sidebar" aria-label="Navigasi {{ $role }}">
    <div class="sidebar-header">
        <div>
            <p class="sidebar-eyebrow">Menu {{ $role }}</p>
            <p class="sidebar-title">{{ auth()->user()->name }}</p>
        </div>
        <button class="icon-button lg:hidden" type="button" data-sidebar-close aria-label="Tutup sidebar" title="Tutup sidebar">
            <span class="icon-close" aria-hidden="true"></span>
        </button>
    </div>

    <nav class="sidebar-nav">
        @foreach($items as $item)
            <a
                class="side-link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}"
                href="{{ route($item['route']) }}"
                title="{{ $item['label'] }}"
            >
                <span class="side-link-icon" aria-hidden="true">{{ mb_substr($item['label'], 0, 1) }}</span>
                <span class="side-link-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
