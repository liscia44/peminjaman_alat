@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="mb-8">
        <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
            Transaksi
        </p>
        <h2 class="font-serif text-ink text-3xl font-normal leading-none">
            Peminjaman Alat
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

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ══ KIRI: FORM PEMINJAMAN ══ --}}
        <div class="lg:col-span-2">
            <div class="bg-paper border border-rule">

                {{-- Form Header --}}
                <div class="px-6 py-5 border-b border-rule">
                    <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                        Formulir
                    </p>
                    <h3 class="font-serif text-ink text-xl font-normal leading-none">
                        Ajukan Peminjaman
                    </h3>
                </div>

                {{-- Form Body --}}
                <form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm" class="px-6 py-6 space-y-6">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    {{-- Pilih Alat --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Alat <span class="text-espresso">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="alat_id" id="alat_select" required
                                class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                            >
                                <option value="">Pilih Alat</option>
                                @foreach($alats as $alat)
                                    <option value="{{ $alat->alat_id }}"
                                        data-max="{{ $alat->stok_tersedia }}"
                                        data-nama="{{ $alat->nama_alat }}">
                                        {{ $alat->nama_alat }} (Tersedia: {{ $alat->stok_tersedia }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                        </div>
                        @error('alat_id')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Kelas <span class="text-espresso">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="kelas" id="kelas_select" required
                                class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                            >
                                <option value="">Pilih Kelas</option>
                                <option value="10-A">10-A</option>
                                <option value="10-B">10-B</option>
                                <option value="10-C">10-C</option>
                                <option value="11-A">11-A</option>
                                <option value="11-B">11-B</option>
                                <option value="11-C">11-C</option>
                                <option value="12-A">12-A</option>
                                <option value="12-B">12-B</option>
                                <option value="12-C">12-C</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                        </div>
                        @error('kelas')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mata Pelajaran --}}
                <div>
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Mata Pelajaran <span class="text-espresso">*</span>
                    </label>
                    <div class="relative">
                        <select
                            name="mata_pelajaran" id="mata_pelajaran_select" required
                            class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                        >
                            <option value="">Pilih Mata Pelajaran</option>
                            <option value="Matematika">Matematika</option>
                            <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                            <option value="Bahasa Inggris">Bahasa Inggris</option>
                            <option value="Fisika">Fisika</option>
                            <option value="Kimia">Kimia</option>
                            <option value="Biologi">Biologi</option>
                            <option value="Sejarah">Sejarah</option>
                            <option value="Geografi">Geografi</option>
                            <option value="Ekonomi">Ekonomi</option>
                            <option value="Sosiologi">Sosiologi</option>
                            <option value="Seni">Seni</option>
                            <option value="Olahraga">Olahraga</option>
                            <option value="TIK">TIK</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                    </div>
                    @error('mata_pelajaran')
                        <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                        {{-- Jam Peminjaman --}}
                        <div>
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Jam Peminjaman <span class="text-espresso">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    name="jam_peminjaman" id="jam_peminjaman_select" required
                                    class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                                >
                                    <option value="">Pilih Jam Pelajaran</option>
                                    <option value="07:00 - 08:30">07:00 - 08:30 (Jam 1)</option>
                                    <option value="08:30 - 10:00">08:30 - 10:00 (Jam 2)</option>
                                    <option value="10:00 - 11:30">10:00 - 11:30 (Jam 3)</option>
                                    <option value="11:30 - 13:00">11:30 - 13:00 (Jam 4)</option>
                                    <option value="13:00 - 14:30">13:00 - 14:30 (Jam 5)</option>
                                    <option value="14:30 - 16:00">14:30 - 16:00 (Jam 6)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                            </div>
                            @error('jam_peminjaman')
                                <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                    {{-- Jumlah --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Jumlah <span class="text-espresso">*</span>
                        </label>
                        <input
                            type="number" id="jumlah_input" name="jumlah" min="1" required
                            placeholder="Jumlah unit yang dipinjam"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        <p id="stok_info" class="font-sans text-[0.62rem] text-label mt-1.5"></p>
                        @error('jumlah')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Peminjaman & Kembali --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Tgl. Pinjam <span class="text-espresso">*</span>
                            </label>
                            <input
                                type="date" name="tanggal_peminjaman" required
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.82rem] text-ink outline-none transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                            @error('tanggal_peminjaman')
                                <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Tgl. Kembali <span class="text-espresso">*</span>
                            </label>
                            <input
                                type="date" name="tanggal_kembali_rencana" required
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.82rem] text-ink outline-none transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                            @error('tanggal_kembali_rencana')
                                <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tujuan --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Tujuan Peminjaman
                        </label>
                        <textarea
                            name="tujuan_peminjaman" rows="3"
                            placeholder="Untuk keperluan..."
                            class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200 resize-none"
                        ></textarea>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="relative w-full overflow-hidden bg-espresso px-6 py-3.5
                               font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase text-paper
                               flex items-center justify-center gap-2
                               transition-colors duration-200 hover:bg-ink active:scale-[0.99]
                               after:content-[''] after:absolute after:inset-0 after:bg-white/[0.06]
                               after:-translate-x-full after:transition-transform after:duration-300
                               hover:after:translate-x-0"
                    >
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Ajukan Peminjaman</span>
                    </button>

                </form>
            </div>
        </div>

        {{-- ══ KANAN: RIWAYAT PEMINJAMAN ══ --}}
        <div class="lg:col-span-3">
            <div class="bg-paper border border-rule flex flex-col">

                {{-- Riwayat Header --}}
                <div class="px-6 py-5 border-b border-rule flex-shrink-0">
                    <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                        Rekam Jejak
                    </p>
                    <h3 class="font-serif text-ink text-xl font-normal leading-none">
                        Riwayat Peminjaman
                    </h3>
                </div>

                @if($peminjaman->isEmpty())
                    {{-- Empty State --}}
                    <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                        <div class="w-14 h-14 bg-cream border border-rule flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-xl text-ghost"></i>
                        </div>
                        <p class="font-serif text-ink text-lg font-normal mb-1">Belum ada riwayat</p>
                        <p class="font-sans text-[0.7rem] text-label tracking-wide">
                            Ajukan peminjaman pertama kamu melalui form di samping.
                        </p>
                    </div>

                @else
                    <div class="divide-y divide-rule overflow-y-auto max-h-[600px]">
                        @foreach($peminjaman as $item)
                            <div class="px-6 py-5 hover:bg-cream/50 transition-colors duration-150">

                                {{-- Top Row --}}
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-serif text-ink text-base font-normal leading-snug truncate">
                                            {{ $item->alat->nama_alat }}
                                        </h4>
                                        <p class="font-sans text-[0.62rem] text-label tracking-wide mt-0.5">
                                            {{ $item->tanggal_peminjaman->format('d M Y') }}
                                            <span class="mx-1 text-ghost">→</span>
                                            {{ $item->tanggal_kembali_rencana->format('d M Y') }}
                                        </p>
                                    </div>

                                    {{-- Status Badge --}}
                                    @if($item->status == 'disetujui')
                                        <span class="flex-shrink-0 px-2.5 py-1 border border-ink/20 bg-ink/5 font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase text-ink">
                                            Disetujui
                                        </span>
                                    @elseif($item->status == 'menunggu')
                                        <span class="flex-shrink-0 px-2.5 py-1 border border-dim/20 bg-dim/5 font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase text-dim">
                                            Menunggu
                                        </span>
                                    @elseif($item->status == 'ditolak')
                                        <span class="flex-shrink-0 px-2.5 py-1 border border-espresso/20 bg-espresso/5 font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase text-espresso">
                                            Ditolak
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="flex-shrink-0 px-2.5 py-1 border border-rule bg-cream font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase text-label">
                                            Dikembalikan
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta Info --}}
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div class="bg-cream px-3 py-2">
                                        <p class="font-sans text-[0.52rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">
                                            Jumlah
                                        </p>
                                        <p class="font-sans text-[0.78rem] font-medium text-ink">
                                            {{ $item->jumlah }} unit
                                        </p>
                                    </div>
                                    <div class="bg-cream px-3 py-2">
                                        <p class="font-sans text-[0.52rem] font-semibold tracking-[0.2em] uppercase text-ghost mb-1">
                                            Disetujui oleh
                                        </p>
                                        <p class="font-sans text-[0.78rem] font-medium text-ink">
                                            {{ $item->petugas->username ?? '—' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Tujuan --}}
                                @if($item->tujuan_peminjaman)
                                    <p class="font-sans text-[0.7rem] text-label leading-relaxed mb-2">
                                        <span class="font-semibold text-dim">Tujuan:</span>
                                        {{ $item->tujuan_peminjaman }}
                                    </p>
                                @endif

                                {{-- Status Note --}}
                                @if($item->status == 'menunggu')
                                    <p class="font-sans text-[0.62rem] text-label flex items-center gap-1.5 mt-2">
                                        <i class="fas fa-clock text-ghost text-[0.6rem]"></i>
                                        Menunggu persetujuan petugas
                                    </p>
                                @elseif($item->status == 'ditolak')
                                    <p class="font-sans text-[0.62rem] text-espresso flex items-center gap-1.5 mt-2">
                                        <i class="fas fa-times text-[0.6rem]"></i>
                                        Peminjaman ditolak
                                    </p>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

    </div>

    <script>
        document.getElementById('alat_select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const maxStok = selected.getAttribute('data-max');
            const jumlahInput = document.getElementById('jumlah_input');
            const stokInfo = document.getElementById('stok_info');

            if (maxStok) {
                jumlahInput.max = maxStok;
                stokInfo.textContent = 'Maksimal: ' + maxStok + ' unit tersedia';
            } else {
                jumlahInput.max = '';
                stokInfo.textContent = '';
            }
        });
    </script>

@endsection