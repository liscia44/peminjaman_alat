@extends('layouts.app')

@section('title', 'Daftar Alat')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                Manajemen Inventaris
            </p>
            <h2 class="font-serif text-ink text-3xl font-normal leading-none">
                Daftar Alat
            </h2>
            <div class="mt-3 h-px w-10 bg-rule"></div>
        </div>

        {{-- ✅ UPDATED: Only Admin can add alat --}}
        @if($userLevel == 'admin')
            <button
                onclick="openModal()"
                class="relative overflow-hidden flex items-center gap-2 bg-espresso px-5 py-3
                       font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-paper
                       transition-colors duration-200 hover:bg-ink active:scale-[0.99]
                       after:content-[''] after:absolute after:inset-0 after:bg-white/[0.06]
                       after:-translate-x-full after:transition-transform after:duration-300
                       hover:after:translate-x-0"
            >
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Alat</span>
            </button>
        @endif
    </div>

    {{-- ══ SUCCESS ALERT ══ --}}
    @if(session('success'))
        <div class="flex items-center justify-between border-l-2 border-espresso bg-cream px-4 py-3 mb-6">
            <span class="font-sans text-[0.75rem] tracking-wide text-ink">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-label hover:text-ink transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    {{-- ══ ERROR ALERT ══ --}}
    @if($errors->any())
        <div class="border-l-2 border-espresso bg-cream px-4 py-3 mb-6">
            @foreach($errors->all() as $error)
                <p class="font-sans text-[0.72rem] leading-relaxed tracking-wide text-ink">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- ══ SUMMARY CARDS ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Total Alat --}}
        <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Total Alat</p>
                    <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $alats->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-espresso flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-boxes text-xs text-paper"></i>
                </div>
            </div>
            <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
        </div>

        {{-- Stok Tersedia --}}
        <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Stok Tersedia</p>
                    <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $alats->sum('stok_tersedia') }}</p>
                </div>
                <div class="w-10 h-10 bg-espresso flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-xs text-paper"></i>
                </div>
            </div>
            <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
        </div>

        {{-- Sedang Dipinjam --}}
        <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Dipinjam</p>
                    <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $alats->sum('stok_total') - $alats->sum('stok_tersedia') }}</p>
                </div>
                <div class="w-10 h-10 bg-dim flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hand-holding text-xs text-paper"></i>
                </div>
            </div>
            <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
        </div>

        {{-- Stok Habis --}}
        <div class="bg-paper border border-rule p-5 group hover:border-espresso/30 transition-colors duration-200">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label mb-2">Stok Habis</p>
                    <p class="font-serif text-[1.9rem] font-normal leading-none text-ink">{{ $alats->where('stok_tersedia', 0)->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-ghost flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-xs text-paper"></i>
                </div>
            </div>
            <div class="mt-4 h-px w-0 bg-espresso/20 group-hover:w-full transition-all duration-500"></div>
        </div>

    </div>

    {{-- ══ ALAT GRID ══ --}}
    @if($alats->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($alats as $alat)
                @php
                    $percentage = $alat->stok_total > 0
                        ? ($alat->stok_tersedia / $alat->stok_total) * 100
                        : 0;
                @endphp

                <div class="bg-paper border border-rule group hover:border-espresso/30 transition-all duration-200 flex flex-col">

                    {{-- Card Header --}}
                    <div class="bg-espresso px-5 py-4 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-serif text-paper text-lg font-normal leading-snug truncate">
                                {{ $alat->nama_alat }}
                            </h3>
                            <p class="font-sans text-[0.6rem] tracking-[0.2em] uppercase text-paper/40 mt-0.5">
                                {{ $alat->kategori->nama_kategori ?? '-' }}
                            </p>
                        </div>

                        {{-- Kondisi Badge --}}
                        @if($alat->kondisi == 'baik')
                            <span class="flex-shrink-0 px-2.5 py-1 bg-paper/10 border border-paper/20 text-paper font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase">
                                Baik
                            </span>
                        @elseif($alat->kondisi == 'rusak')
                            <span class="flex-shrink-0 px-2.5 py-1 bg-paper/5 border border-paper/10 text-paper/50 font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase">
                                Rusak
                            </span>
                        @else
                            <span class="flex-shrink-0 px-2.5 py-1 bg-paper/5 border border-paper/10 text-paper/30 font-sans text-[0.55rem] font-semibold tracking-[0.15em] uppercase">
                                Hilang
                            </span>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex-1 flex flex-col gap-4">

                        {{-- Kode & Lokasi --}}
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-barcode text-ghost text-xs"></i>
                                <span class="font-sans text-[0.7rem] text-label tracking-wide">{{ $alat->kode_alat }}</span>
                            </div>
                            @if($alat->lokasi)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-ghost text-xs"></i>
                                    <span class="font-sans text-[0.7rem] text-label tracking-wide">{{ $alat->lokasi }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Stok Info --}}
                        <div class="bg-cream p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-sans text-[0.58rem] font-semibold tracking-[0.2em] uppercase text-label">
                                    Ketersediaan Stok
                                </span>
                                @if($alat->stok_tersedia == 0)
                                    <span class="font-sans text-[0.68rem] font-semibold text-espresso">
                                        {{ $alat->stok_tersedia }} / {{ $alat->stok_total }}
                                    </span>
                                @elseif($alat->stok_tersedia < ($alat->stok_total * 0.3))
                                    <span class="font-sans text-[0.68rem] font-semibold text-dim">
                                        {{ $alat->stok_tersedia }} / {{ $alat->stok_total }}
                                    </span>
                                @else
                                    <span class="font-sans text-[0.68rem] font-semibold text-ink">
                                        {{ $alat->stok_tersedia }} / {{ $alat->stok_total }}
                                    </span>
                                @endif
                            </div>

                            {{-- Progress Bar --}}
                            <div class="w-full bg-rule h-1.5">
                                @if($percentage == 0)
                                    <div class="h-1.5 bg-espresso transition-all" style="width: {{ $percentage }}%"></div>
                                @elseif($percentage < 30)
                                    <div class="h-1.5 bg-dim transition-all" style="width: {{ $percentage }}%"></div>
                                @else
                                    <div class="h-1.5 bg-ink transition-all" style="width: {{ $percentage }}%"></div>
                                @endif
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="flex flex-wrap gap-2">
                            @if($alat->stok_tersedia == 0)
                                <span class="px-2.5 py-1 border border-espresso/30 bg-espresso/5 font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-espresso">
                                    <i class="fas fa-times-circle mr-1"></i>Stok Habis
                                </span>
                            @elseif($alat->stok_tersedia < ($alat->stok_total * 0.3))
                                <span class="px-2.5 py-1 border border-dim/30 bg-dim/5 font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-dim">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Stok Menipis
                                </span>
                            @else
                                <span class="px-2.5 py-1 border border-rule bg-cream font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-label">
                                    <i class="fas fa-check mr-1"></i>Tersedia
                                </span>
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        @if($alat->deskripsi)
                            <p class="font-sans text-[0.7rem] leading-relaxed text-label line-clamp-2">
                                {{ $alat->deskripsi }}
                            </p>
                        @endif

                        {{-- ✅ UPDATED: Action Buttons - Only Admin can edit/delete --}}
                        @if($userLevel == 'admin')
                            <div class="flex gap-2 pt-3 border-t border-rule mt-auto">
                                <button
                                    onclick="editAlat({{ $alat->alat_id }}, {{ json_encode($alat) }})"
                                    class="flex-1 flex items-center justify-center gap-2 border border-espresso bg-transparent
                                           px-3 py-2 font-sans text-[0.6rem] font-semibold tracking-[0.15em] uppercase text-espresso
                                           hover:bg-espresso hover:text-paper transition-all duration-200"
                                >
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Edit</span>
                                </button>
                                <form action="{{ route('alat.destroy', $alat->alat_id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-full flex items-center justify-center gap-2 border border-rule bg-transparent
                                               px-3 py-2 font-sans text-[0.6rem] font-semibold tracking-[0.15em] uppercase text-label
                                               hover:border-espresso hover:text-espresso transition-all duration-200"
                                    >
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="bg-paper border border-rule p-16 text-center">
            <div class="w-16 h-16 bg-cream border border-rule flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-boxes text-2xl text-ghost"></i>
            </div>
            <p class="font-serif text-ink text-xl font-normal mb-2">Belum ada data alat</p>
            <p class="font-sans text-[0.72rem] text-label tracking-wide">
                Klik tombol "Tambah Alat" untuk menambahkan alat baru.
            </p>
        </div>
    @endif

    {{-- ══ MODAL TAMBAH / EDIT ══ --}}
    <div id="alatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8" style="background:rgba(26,23,20,0.55)">
        <div class="relative w-full max-w-lg bg-paper border border-rule shadow-2xl flex flex-col max-h-[90vh] animate-fade-up">

            {{-- Modal Header --}}
            <div class="flex-shrink-0 flex items-end justify-between px-8 pt-7 pb-5 border-b border-rule">
                <div>
                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                        Formulir
                    </p>
                    <h3 id="modalTitle" class="font-serif text-ink text-2xl font-normal leading-none">
                        Tambah Alat
                    </h3>
                </div>
                <button
                    onclick="closeModal()"
                    class="w-7 h-7 flex items-center justify-center text-ghost hover:text-ink border border-rule hover:border-espresso transition-all duration-150 mb-0.5"
                >
                    <i class="fas fa-times text-[0.6rem]"></i>
                </button>
            </div>

            {{-- Modal Body (scrollable) --}}
            <form id="alatForm" method="POST" action="{{ route('alat.store') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">

                    {{-- Nama Alat --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Nama Alat
                        </label>
                        <input
                            type="text" id="nama_alat" name="nama_alat" required
                            placeholder="Masukkan nama alat"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                    </div>

                    {{-- 2 kolom: Kategori & Kondisi --}}
                    <div class="grid grid-cols-2 gap-5">

                        {{-- Kategori --}}
                        <div>
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Kategori
                            </label>
                            <div class="relative">
                                <select
                                    id="kategori_id" name="kategori_id" required
                                    class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.8rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                                >
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\Kategori::all() as $kat)
                                        <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- Kondisi --}}
                        <div>
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Kondisi
                            </label>
                            <div class="relative">
                                <select
                                    id="kondisi" name="kondisi" required
                                    class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.8rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer"
                                >
                                    <option value="">Pilih Kondisi</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                            </div>
                        </div>

                    </div>

                    {{-- 2 kolom: Kode & Stok --}}
                    <div class="grid grid-cols-2 gap-5">

                        {{-- Kode Alat --}}
                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Kode Alat
                            </label>
                            <input
                                type="text" id="kode_alat" name="kode_alat" required
                                placeholder="Contoh: ALAT-001"
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        </div>

                        {{-- Stok Total --}}
                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Stok Total
                            </label>
                            <input
                                type="number" id="stok_total" name="stok_total" min="1" required
                                placeholder="0"
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        </div>

                    </div>

                    {{-- Lokasi --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Lokasi
                        </label>
                        <input
                            type="text" id="lokasi" name="lokasi"
                            placeholder="Contoh: Gudang A"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Deskripsi
                        </label>
                        <textarea
                            id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Deskripsi singkat mengenai alat ini"
                            class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200 resize-none"
                        ></textarea>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex-shrink-0 flex gap-3 px-8 py-5 border-t border-rule bg-paper">
                    <button
                        type="submit"
                        class="flex-1 bg-espresso text-paper font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:bg-ink transition-colors duration-200"
                    >
                        Simpan
                    </button>
                    <button
                        type="button" onclick="closeModal()"
                        class="flex-1 border border-rule text-label font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:border-espresso hover:text-espresso transition-all duration-200"
                    >
                        Batal
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('alatModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Tambah Alat';
            document.getElementById('alatForm').action = '{{ route("alat.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('nama_alat').value = '';
            document.getElementById('kategori_id').value = '';
            document.getElementById('kode_alat').value = '';
            document.getElementById('stok_total').value = '';
            document.getElementById('kondisi').value = '';
            document.getElementById('lokasi').value = '';
            document.getElementById('deskripsi').value = '';
        }

        function closeModal() {
            document.getElementById('alatModal').classList.add('hidden');
        }

        function editAlat(id, data) {
            document.getElementById('alatModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Alat';
            document.getElementById('alatForm').action = '/alat/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('nama_alat').value = data.nama_alat;
            document.getElementById('kategori_id').value = data.kategori_id;
            document.getElementById('kode_alat').value = data.kode_alat;
            document.getElementById('stok_total').value = data.stok_total;
            document.getElementById('kondisi').value = data.kondisi;
            document.getElementById('lokasi').value = data.lokasi || '';
            document.getElementById('deskripsi').value = data.deskripsi || '';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('alatModal');
            if (event.target == modal) closeModal();
        }
    </script>

@endsection