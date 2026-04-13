@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="mb-8">
        <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
            Manajemen Transaksi
        </p>
        <h2 class="font-serif text-ink text-3xl font-normal leading-none">
            Tracking Peminjaman
        </h2>
        <div class="mt-3 h-px w-10 bg-rule"></div>
    </div>

    {{-- ══ SUCCESS ALERT ══ --}}
    @if(session('success'))
        <div class="flex items-center justify-between border-l-2 border-espresso bg-cream px-4 py-3 mb-6">
            <span class="font-sans text-[0.75rem] tracking-wide text-ink">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-label hover:text-ink transition-colors ml-4">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    {{-- ══ CALCULATE STATISTICS ══ --}}
    @php
        $peminjamanMenunggu = $allPeminjaman->filter(fn($p) => $p->status === 'menunggu');
        $peminjamanAktif = $allPeminjaman->filter(fn($p) => $p->status === 'disetujui');
        $peminjamanSelesai = $allPeminjaman->filter(fn($p) => $p->status === 'dikembalikan');
    @endphp

    {{-- ══ SUMMARY CARDS ══ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

    <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Sedang Dipinjam</p>
                <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $peminjamanAktif->count() }}</p>
            </div>
            <div class="w-10 h-10 bg-espresso flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-xs text-paper"></i>
            </div>
        </div>
        <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
    </div>

    <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Sudah Dikembalikan</p>
                <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $peminjamanSelesai->count() }}</p>
            </div>
            <div class="w-10 h-10 bg-ghost flex items-center justify-center flex-shrink-0">
                <i class="fas fa-undo-alt text-xs text-paper"></i>
            </div>
        </div>
        <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
    </div>

</div>

    {{-- ══ TABS ══ --}}
    <div class="border-b border-rule mb-6">
        <div class="flex gap-0">

            {{-- ✅ SEMBUNYIKAN TAB MENUNGGU --}}
            {{-- <button onclick="showTab('menunggu')" ... --}}

            <button onclick="showTab('aktif')" id="btn-aktif"
                class="group relative px-6 py-3.5 font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase flex items-center gap-2 transition-colors duration-150 border-b-2 border-espresso text-ink">
                <i class="fas fa-clipboard-check text-xs"></i>
                <span>Sedang Dipinjam</span>
                <span class="bg-espresso text-paper font-sans text-[0.5rem] font-bold px-1.5 py-0.5 tracking-wide">
                    {{ $peminjamanAktif->count() }}
                </span>
            </button>

            <button onclick="showTab('selesai')" id="btn-selesai"
                class="group relative px-6 py-3.5 font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase flex items-center gap-2 transition-colors duration-150 border-b-2 border-transparent text-label hover:text-ink">
                <i class="fas fa-check-double text-xs"></i>
                <span>Selesai</span>
                <span class="bg-rule text-label font-sans text-[0.5rem] font-bold px-1.5 py-0.5 tracking-wide">
                    {{ $peminjamanSelesai->count() }}
                </span>
            </button>

        </div>
    </div>

    {{-- ══ TAB: MENUNGGU PERSETUJUAN ══ --}}
    <div id="tab-menunggu" class="tab-content">
        @if($peminjamanMenunggu->isEmpty())
            <div class="bg-paper border border-rule p-14 text-center">
                <div class="w-12 h-12 bg-cream border border-rule flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-ghost text-base"></i>
                </div>
                <p class="font-serif text-ink text-lg font-normal mb-1">Semua sudah diproses</p>
                <p class="font-sans text-[0.7rem] text-label tracking-wide">Tidak ada peminjaman yang menunggu persetujuan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($peminjamanMenunggu as $item)
                    <div class="bg-paper border border-rule group hover:border-espresso/30 transition-colors duration-200 flex flex-col">

                        {{-- Card Header --}}
                        <div class="bg-espresso px-5 py-4 flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-serif text-paper text-base font-normal leading-snug truncate">
                                    {{ $item->alat->nama_alat }}
                                </h3>
                                <p class="font-sans text-[0.58rem] tracking-[0.2em] uppercase text-paper/40 mt-0.5">
                                    @if($item->isGuest())
                                        <span class="bg-paper text-espresso px-1.5 py-0.5 rounded text-[0.5rem] font-bold">GUEST</span>
                                        {{ $item->nama_peminjam_guest }}
                                    @else
                                        {{ $item->user->username }}
                                    @endif
                                </p>
                            </div>
                            <span class="flex-shrink-0 px-2.5 py-1 border border-paper/20 bg-paper/10 font-sans text-[0.52rem] font-semibold tracking-[0.15em] uppercase text-paper/70">
                                Menunggu
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5 flex-1 flex flex-col gap-4">

                            {{-- Kode Peminjaman --}}
                            <div class="bg-cream px-3 py-2">
                                <p class="font-mono text-[0.6rem] text-espresso font-semibold tracking-wider">
                                    {{ $item->kode_peminjaman }}
                                </p>
                            </div>

                            {{-- Detail Info --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-cream px-3 py-2">
                                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">Jumlah</p>
                                    <p class="font-sans text-[0.78rem] font-medium text-ink">{{ $item->jumlah }} unit</p>
                                </div>
                                <div class="bg-cream px-3 py-2">
                                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">Tgl. Pinjam</p>
                                    <p class="font-sans text-[0.78rem] font-medium text-ink">{{ $item->tanggal_peminjaman->format('d M Y') }}</p>
                                </div>
                                <div class="bg-cream px-3 py-2 col-span-2">
                                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">Tgl. Kembali</p>
                                    <p class="font-sans text-[0.78rem] font-medium text-ink">{{ $item->tanggal_kembali_rencana->format('d M Y') }}</p>
                                </div>
                            </div>

                            {{-- Kontak Guest --}}
                            @if($item->isGuest())
                                <div class="border-l-2 border-espresso pl-3">
                                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">Kontak</p>
                                    <p class="font-sans text-[0.72rem] text-label">
                                        <i class="fas fa-phone text-espresso mr-1"></i>
                                        {{ $item->telepon_peminjam_guest }}
                                    </p>
                                </div>
                            @endif

                            {{-- Tujuan --}}
                            @if($item->tujuan_peminjaman)
                                <div class="border-l-2 border-rule pl-3">
                                    <p class="font-sans text-[0.52rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">Tujuan</p>
                                    <p class="font-sans text-[0.72rem] text-label leading-relaxed">{{ $item->tujuan_peminjaman }}</p>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 pt-3 border-t border-rule mt-auto">
                                <button
                                    onclick="approvePeminjaman({{ $item->peminjaman_id }})"
                                    class="flex-1 flex items-center justify-center gap-2 bg-espresso text-paper
                                           px-3 py-2.5 font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase
                                           hover:bg-ink transition-colors duration-200"
                                >
                                    <i class="fas fa-check text-xs"></i>
                                    <span>Setujui</span>
                                </button>
                                <button
                                    onclick="rejectPeminjaman({{ $item->peminjaman_id }})"
                                    class="flex-1 flex items-center justify-center gap-2 border border-rule text-label
                                           px-3 py-2.5 font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase
                                           hover:border-espresso hover:text-espresso transition-all duration-200"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                    <span>Tolak</span>
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

   {{-- ══ TAB: SEDANG DIPINJAM ══ --}}
<div id="tab-aktif" class="tab-content hidden">
    @if($peminjamanAktif->isEmpty())
        <div class="bg-paper border border-rule p-14 text-center">
            <div class="w-12 h-12 bg-cream border border-rule flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-ghost text-base"></i>
            </div>
            <p class="font-serif text-ink text-lg font-normal mb-1">Tidak ada alat dipinjam</p>
            <p class="font-sans text-[0.7rem] text-label tracking-wide">Semua alat sedang tersedia.</p>
        </div>
    @else
        <div class="bg-paper border border-rule overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-rule bg-cream">
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Alat</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Unit</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Peminjam</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Jumlah</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Durasi</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Status Waktu</th>
                        <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule">
    @foreach($peminjamanAktif as $item)
        @php
            $jamPelajaran = [
                '07:00' => 1, '08:30' => 2, '10:00' => 3, 
                '11:30' => 4, '13:00' => 5, '14:30' => 6
            ];
            
            $jamMulaiStr = trim(explode(' - ', $item->jam_peminjaman)[0] ?? '07:00');
            $jamSelesaiStr = '16:00';
            if ($item->jam_kembali) {
                $jamSelesaiStr = trim(explode(' - ', $item->jam_kembali)[0] ?? '16:00');
            }
            
            $jamMulaiNum = $jamPelajaran[$jamMulaiStr] ?? null;
            $jamSelesaiNum = $jamPelajaran[$jamSelesaiStr] ?? null;
            
            $durasiJam = ($jamMulaiNum && $jamSelesaiNum) 
                ? ($jamSelesaiNum - $jamMulaiNum) 
                : 'N/A';
        @endphp
        
        <tr class="hover:bg-cream/40 transition-colors duration-100">

            <td class="px-5 py-4 font-sans text-[0.78rem] font-medium text-ink whitespace-nowrap">
                {{ $item->alat->nama_alat }}
            </td>

            {{-- ✅ TAMBAH: Unit Number --}}
            <td class="px-5 py-4 whitespace-nowrap">
                @if($item->alatUnit)
                    <div class="bg-cream px-3 py-2 rounded inline-block">
                        <p class="font-sans text-[0.75rem] font-bold text-ink">
                            <i class="fas fa-tag text-[0.6rem] text-label mr-1"></i>
                            Unit #{{ $item->alatUnit->unit_number }}
                        </p>
                    </div>
                @else
                    <span class="font-sans text-[0.65rem] text-ghost italic">Tidak ada unit</span>
                @endif
            </td>
            
            <td class="px-5 py-4 font-sans text-[0.78rem] text-label whitespace-nowrap">
                @if($item->isGuest())
                    <span class="bg-espresso text-paper px-1.5 py-0.5 rounded text-[0.5rem] font-bold mr-1">GUEST</span>
                    {{ $item->nama_peminjam_guest }}
                @else
                    {{ $item->user->username }}
                @endif
            </td>
            
            <td class="px-5 py-4 font-sans text-[0.78rem] text-label whitespace-nowrap">
                {{ $item->jumlah }} unit
            </td>

            <td class="px-5 py-4 whitespace-nowrap">
                <div class="bg-cream px-3 py-2 rounded">
                    <p class="font-sans text-[0.78rem] font-medium text-ink">
                        @if($jamMulaiNum && $jamSelesaiNum)
                            Jam {{ $jamMulaiNum }} - Jam {{ $jamSelesaiNum }}
                        @else
                            {{ $jamMulaiStr }} - {{ $jamSelesaiStr }}
                        @endif
                    </p>
                    <p class="font-sans text-[0.65rem] text-label mt-0.5">
                        @if(is_numeric($durasiJam))
                            {{ $durasiJam }} jam pelajaran
                        @else
                            Durasi: N/A
                        @endif
                    </p>
                </div>
            </td>

            <td class="px-5 py-4 whitespace-nowrap">
                <div class="px-3 py-2 bg-ghost/10 border border-ghost/30 rounded">
                    <p class="font-sans text-[0.65rem] font-semibold text-ghost">
                        <i class="fas fa-hourglass-end text-[0.5rem]"></i>
                        Masih Dipinjam
                    </p>
                    <p class="font-sans text-[0.6rem] text-ghost/70">
                        Hingga jam {{ $jamSelesaiStr }}
                    </p>
                </div>
            </td>
            
            <td class="px-5 py-4 whitespace-nowrap">
                <a href="{{ route('pengembalian.index') }}"
                    class="flex items-center gap-1.5 border border-espresso px-3 py-1.5
                           font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-espresso
                           hover:bg-espresso hover:text-paper transition-all duration-200 whitespace-nowrap">
                    <i class="fas fa-undo text-xs"></i>
                    Catat Kembali
                </a>
            </td>
        </tr>
    @endforeach
</tbody>
            </table>
        </div>
    @endif
</div>

    {{-- ══ TAB: SELESAI ══ --}}
    <div id="tab-selesai" class="tab-content hidden">
        @if($peminjamanSelesai->isEmpty())
            <div class="bg-paper border border-rule p-14 text-center">
                <div class="w-12 h-12 bg-cream border border-rule flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-ghost text-base"></i>
                </div>
                <p class="font-serif text-ink text-lg font-normal mb-1">Belum ada yang selesai</p>
                <p class="font-sans text-[0.7rem] text-label tracking-wide">Data peminjaman yang selesai akan muncul di sini.</p>
            </div>
        @else
            <div class="bg-paper border border-rule overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-rule bg-cream">
                            <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Alat</th>
                            <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Peminjam</th>
                            <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Tgl. Pinjam</th>
                            <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Tgl. Kembali</th>
                            <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule">
                        @foreach($peminjamanSelesai as $item)
                            <tr class="hover:bg-cream/40 transition-colors duration-100">
                                <td class="px-5 py-4 font-sans text-[0.78rem] font-medium text-ink whitespace-nowrap">
                                    {{ $item->alat->nama_alat }}
                                </td>
                                <td class="px-5 py-4 font-sans text-[0.78rem] text-label whitespace-nowrap">
                                    @if($item->isGuest())
                                        <span class="bg-espresso text-paper px-1.5 py-0.5 rounded text-[0.5rem] font-bold mr-1">GUEST</span>
                                        {{ $item->nama_peminjam_guest }}
                                    @else
                                        {{ $item->user->username }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-sans text-[0.78rem] text-label whitespace-nowrap">
                                    {{ $item->tanggal_peminjaman->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 font-sans text-[0.78rem] text-label whitespace-nowrap">
                                    —
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 border border-rule bg-cream font-sans text-[0.52rem] font-semibold tracking-[0.15em] uppercase text-label">
                                        Dikembalikan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

    // Reset semua tab button
    ['aktif', 'selesai'].forEach(t => {
        const btn = document.getElementById('btn-' + t);
        btn.classList.remove('border-espresso', 'text-ink');
        btn.classList.add('border-transparent', 'text-label');

        const badge = btn.querySelector('span:last-child');
        if (badge) {
            badge.classList.remove('bg-espresso', 'text-paper');
            badge.classList.add('bg-rule', 'text-label');
        }
    });

    // Aktifkan tab yang dipilih
    document.getElementById('tab-' + tab).classList.remove('hidden');
    const activeBtn = document.getElementById('btn-' + tab);
    activeBtn.classList.remove('border-transparent', 'text-label');
    activeBtn.classList.add('border-espresso', 'text-ink');

    const activeBadge = activeBtn.querySelector('span:last-child');
    if (activeBadge) {
        activeBadge.classList.remove('bg-rule', 'text-label');
        activeBadge.classList.add('bg-espresso', 'text-paper');
    }
}

// Default open tab 'aktif'
document.addEventListener('DOMContentLoaded', () => {
    showTab('aktif');
});

        function approvePeminjaman(id) {
            if (confirm('Setujui peminjaman ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/peminjaman/' + id + '/approve';

                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden'; method.name = '_method';
                method.value = 'PATCH';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectPeminjaman(id) {
            if (confirm('Tolak peminjaman ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/peminjaman/' + id;

                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden'; method.name = '_method';
                method.value = 'PUT';

                const status = document.createElement('input');
                status.type = 'hidden'; status.name = 'status';
                status.value = 'ditolak';

                form.appendChild(csrf);
                form.appendChild(method);
                form.appendChild(status);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

@endsection