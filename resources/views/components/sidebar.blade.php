@php
    $userLevel = strtolower(auth()->user()->level ?? '');

    $navGroups = [];

    $navGroups[] = [
        'label' => 'Utama',
        'items' => [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'match' => 'dashboard'],
        ]
    ];

    $inventaris = [];
    // ✅ UPDATED: Add petugas to alat menu
    if (in_array($userLevel, ['admin', 'petugas', 'peminjam'])) {
        $inventaris[] = ['route' => 'alat.index', 'label' => 'Alat', 'icon' => 'fa-wrench', 'match' => 'alat.*'];
    }
    if ($userLevel === 'admin') {
        $inventaris[] = ['route' => 'kategori.index', 'label' => 'Kategori', 'icon' => 'fa-folder-open', 'match' => 'kategori.*'];
    }
    if (!empty($inventaris)) {
        $navGroups[] = ['label' => 'Inventaris', 'items' => $inventaris];
    }

    $transaksi = [];
    if (in_array($userLevel, ['admin', 'petugas', 'peminjam'])) {
        $transaksi[] = ['route' => 'peminjaman.index', 'label' => 'Peminjaman', 'icon' => 'fa-clipboard-list', 'match' => 'peminjaman.*'];
        $transaksi[] = ['route' => 'pengembalian.index', 'label' => 'Pengembalian', 'icon' => 'fa-rotate-left', 'match' => 'pengembalian.*'];
    }
    if (!empty($transaksi)) {
        $navGroups[] = ['label' => 'Transaksi', 'items' => $transaksi];
    }

    $admin = [];
    if ($userLevel === 'admin') {
        $admin[] = ['route' => 'users.index', 'label' => 'Pengguna', 'icon' => 'fa-users', 'match' => 'users.*'];
    }
    if (in_array($userLevel, ['admin', 'petugas'])) {
        $admin[] = ['route' => 'laporan.index', 'label' => 'Laporan', 'icon' => 'fa-chart-line', 'match' => 'laporan.*'];
    }
    if ($userLevel === 'admin') {
        $admin[] = ['route' => 'log.index', 'label' => 'Log Aktivitas', 'icon' => 'fa-scroll', 'match' => 'log.*'];
    }
    if (!empty($admin)) {
        $navGroups[] = ['label' => 'Administrasi', 'items' => $admin];
    }
    if ($userLevel === 'admin') {
    $admin[] = ['route' => 'admin.qr-management', 'label' => 'QR Code', 'icon' => 'fa-qrcode', 'match' => 'qr.*'];
}
@endphp

<aside id="sidebar" class="fixed left-0 top-[70px] bottom-0 z-30 w-60 flex-shrink-0 bg-espresso flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- Subtle right edge line --}}
    <div class="absolute right-0 top-0 bottom-0 w-px bg-white/[0.06]"></div>

    {{-- ── NAV GROUPS ── --}}
    <nav class="flex-1 px-3 py-5 overflow-y-auto space-y-5">
        @foreach($navGroups as $group)
            <div>
                <p class="px-4 mb-1.5 font-sans text-[0.42rem] font-semibold tracking-[0.35em] uppercase text-paper/25">
                    {{ $group['label'] }}
                </p>
                <div class="space-y-0.5">
                    @foreach($group['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['match']) || request()->routeIs(explode('.*', $item['match'])[0]);
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="relative flex items-center gap-3 px-4 py-2.5 transition-all duration-150
                                  {{ $isActive
                                      ? 'bg-white/[0.10] text-paper'
                                      : 'text-paper/45 hover:bg-white/[0.05] hover:text-paper/75' }}"
                        >
                            @if($isActive)
                                <span class="absolute left-0 top-1 bottom-1 w-[2px] bg-rule rounded-r-full"></span>
                            @endif
                            <i class="fas {{ $item['icon'] }} text-[0.6rem] w-3.5 text-center flex-shrink-0
                                       {{ $isActive ? 'text-paper/80' : 'text-paper/30' }}"></i>
                            <span class="font-sans text-[0.68rem] font-medium tracking-wide">
                                {{ $item['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- ── FOOTER ── --}}
    <div class="border-t border-white/[0.08] px-7 py-5">
        <p class="font-sans text-[0.42rem] tracking-[0.2em] uppercase text-paper/15">
            &copy; {{ date('Y') }} &nbsp;·&nbsp; Akses Terbatas
        </p>
    </div>

</aside>