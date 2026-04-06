<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Alat - Ajukan Peminjaman</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Cormorant Garamond', 'Georgia', 'serif'],
                        sans:  ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        espresso: '#1c1917',
                        ink:      '#1a1714',
                        dim:      '#4a4540',
                        label:    '#6e665e',
                        rule:     '#c8bfb0',
                        ghost:    '#a89f94',
                        paper:    '#fffdf9',
                        cream:    '#f5f0e8',
                        sand:     '#e8e0d0',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-cream font-sans">

    {{-- ══ NAVBAR ══ --}}
    <nav class="bg-paper border-b border-rule sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            {{-- Logo --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-espresso flex items-center justify-center rounded">
                    <i class="fas fa-toolbox text-paper text-xl"></i>
                </div>
                <div>
                    <h1 class="font-serif text-ink text-2xl font-normal">Peminjaman Alat</h1>
                    <p class="font-sans text-[0.65rem] text-label tracking-widest">SISTEM MANAJEMEN</p>
                </div>
            </div>

            {{-- Login Button --}}
            <a href="{{ route('login') }}" class="px-6 py-2.5 bg-espresso text-paper font-sans text-[0.7rem] font-semibold tracking-[0.1em] uppercase hover:bg-ink transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-sign-in-alt"></i>
                LOGIN ADMIN
            </a>
        </div>
    </nav>

    {{-- ══ MAIN CONTENT ══ --}}
    <main class="min-h-[calc(100vh-70px)]">
        <div class="max-w-7xl mx-auto px-6 py-12">

            {{-- ══ PAGE HEADER ══ --}}
            <div class="mb-12">
                <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                    Transaksi
                </p>
                <h2 class="font-serif text-ink text-3xl font-normal leading-none">
                    Ajukan Peminjaman Alat
                </h2>
                <div class="mt-3 h-px w-10 bg-rule"></div>
            </div>

            {{-- ══ SUCCESS ALERT ══ --}}
            @if(session('success'))
                <div class="flex items-center justify-between border-l-2 border-ink bg-cream px-6 py-5 mb-8">
                    <div class="flex-1">
                        <p class="font-sans text-[0.75rem] tracking-wide text-ink font-semibold mb-3">
                            ✓ Peminjaman Berhasil Diajukan!
                        </p>
                        <p class="font-sans text-[0.75rem] text-label mb-4">
                            {!! str_replace('<strong>', '<strong class="text-ink font-semibold">', session('success')) !!}
                        </p>
                        <div class="bg-ink/5 border border-ink/20 p-4">
                            <p class="font-sans text-[0.65rem] text-label mb-2">Kode Peminjaman Anda:</p>
                            <p class="font-mono text-xl font-bold text-ink tracking-widest">{{ session('kode_peminjaman') }}</p>
                            <p class="font-sans text-[0.65rem] text-label mt-2">Simpan kode ini untuk referensi</p>
                        </div>
                    </div>
                    <button onclick="this.closest('div').remove()" class="text-label hover:text-ink transition-colors ml-4 flex-shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            @endif

            {{-- ══ ERROR ALERT ══ --}}
            @if($errors->any())
                <div class="flex items-center justify-between border-l-2 border-espresso bg-espresso/5 px-6 py-5 mb-8">
                    <div class="flex-1">
                        <p class="font-sans text-[0.75rem] tracking-wide text-espresso font-semibold mb-3">
                            ⚠ Terjadi Kesalahan
                        </p>
                        <ul class="font-sans text-[0.75rem] text-espresso space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.closest('div').remove()" class="text-espresso/60 hover:text-espresso transition-colors ml-4 flex-shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            @endif

            {{-- ══ GRID FORM + INFO ══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ══ FORM SECTION ══ --}}
                <div class="lg:col-span-2">
                    <div class="bg-paper border border-rule">

                        {{-- Form Header --}}
                        <div class="px-6 py-5 border-b border-rule">
                            <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                                Formulir
                            </p>
                            <h3 class="font-serif text-ink text-xl font-normal leading-none">
                                Data Peminjaman
                            </h3>
                        </div>

                        {{-- Form Body --}}
                        <form action="{{ route('peminjaman.guest.store') }}" method="POST" id="peminjamanForm" class="px-6 py-6 space-y-6">
                            @csrf

                            {{-- Nama Lengkap --}}
                            <div class="relative">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Nama Lengkap <span class="text-espresso">*</span>
                                </label>
                                <input
                                    type="text" 
                                    name="nama_peminjam_guest" 
                                    value="{{ old('nama_peminjam_guest') }}" 
                                    required
                                    placeholder="Masukkan nama lengkap"
                                    class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                                >
                                <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                                @error('nama_peminjam_guest')
                                    <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Telepon --}}
                            <div class="relative">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Nomor Telepon <span class="text-espresso">*</span>
                                </label>
                                <input
                                    type="tel" 
                                    name="telepon_peminjam_guest" 
                                    value="{{ old('telepon_peminjam_guest') }}" 
                                    required
                                    placeholder="+62 8xx xxx xxxx"
                                    class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                                >
                                <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                                @error('telepon_peminjam_guest')
                                    <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pilih Alat --}}
                            <div>
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Alat <span class="text-espresso">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        name="alat_id" id="alat_select" required
                                        class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
                                    >
                                        <option value="">Pilih Alat</option>
                                        @foreach($alats as $alat)
                                            <option value="{{ $alat->alat_id }}"
                                                data-max="{{ $alat->stok_tersedia }}"
                                                {{ old('alat_id') == $alat->alat_id ? 'selected' : '' }}>
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

                            {{-- Jumlah --}}
                            <div class="relative">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Jumlah <span class="text-espresso">*</span>
                                </label>
                                <input
                                    type="number" id="jumlah_input" name="jumlah" min="1" required value="{{ old('jumlah', 1) }}"
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
                                        type="date" name="tanggal_peminjaman" required value="{{ old('tanggal_peminjaman') }}"
                                        min="{{ now()->format('Y-m-d') }}"
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
                                        type="date" name="tanggal_kembali_rencana" required value="{{ old('tanggal_kembali_rencana') }}"
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
                                    class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200"
                                >{{ old('tujuan_peminjaman') }}</textarea>
                            </div>

                            {{-- Submit Button --}}
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

                {{-- ══ INFO SECTION ══ --}}
                <div class="lg:col-span-3">
                    <div class="bg-paper border border-rule flex flex-col h-full">

                        {{-- Info Header --}}
                        <div class="px-6 py-5 border-b border-rule flex-shrink-0">
                            <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                                Petunjuk
                            </p>
                            <h3 class="font-serif text-ink text-xl font-normal leading-none">
                                Informasi Peminjaman
                            </h3>
                        </div>

                        {{-- Info Content --}}
                        <div class="px-6 py-6 space-y-8 overflow-y-auto flex-1">
                            {{-- Info Box 1 --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-6 h-6 bg-espresso text-paper flex items-center justify-center rounded-full">
                                        <i class="fas fa-check text-[0.6rem]"></i>
                                    </div>
                                    <h4 class="font-serif text-ink text-base font-normal">Cara Mengajukan</h4>
                                </div>
                                <ol class="font-sans text-[0.7rem] text-label space-y-2 ml-8">
                                    <li>1. Isi semua data pribadi dengan benar</li>
                                    <li>2. Pilih alat yang ingin dipinjam</li>
                                    <li>3. Tentukan tanggal peminjaman & pengembalian</li>
                                    <li>4. Klik tombol "Ajukan Peminjaman"</li>
                                </ol>
                            </div>

                            {{-- Info Box 2 --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-6 h-6 bg-espresso text-paper flex items-center justify-center rounded-full">
                                        <i class="fas fa-clock text-[0.6rem]"></i>
                                    </div>
                                    <h4 class="font-serif text-ink text-base font-normal">Waktu Proses</h4>
                                </div>
                                <div class="font-sans text-[0.7rem] text-label space-y-2 ml-8">
                                    <p>Admin akan memverifikasi data Anda dalam waktu <strong class="text-ink">1-2 hari kerja</strong>.</p>
                                    <p>Kami akan menghubungi Anda melalui nomor telepon yang tertera di formulir.</p>
                                </div>
                            </div>

                            {{-- Info Box 3 --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-6 h-6 bg-espresso text-paper flex items-center justify-center rounded-full">
                                        <i class="fas fa-code text-[0.6rem]"></i>
                                    </div>
                                    <h4 class="font-serif text-ink text-base font-normal">Kode Peminjaman</h4>
                                </div>
                                <p class="font-sans text-[0.7rem] text-label ml-8">Setiap peminjaman memiliki kode unik. Simpan kode ini untuk tracking dan referensi Anda di masa depan.</p>
                            </div>

                            {{-- Info Box 4 --}}
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-6 h-6 bg-espresso text-paper flex items-center justify-center rounded-full">
                                        <i class="fas fa-exclamation text-[0.6rem]"></i>
                                    </div>
                                    <h4 class="font-serif text-ink text-base font-normal">Ketentuan Penting</h4>
                                </div>
                                <ul class="font-sans text-[0.7rem] text-label space-y-1.5 ml-8">
                                    <li>• Pastikan nomor telepon aktif untuk menerima konfirmasi</li>
                                    <li>• Perhatikan tanggal kembali untuk menghindari denda</li>
                                    <li>• Alat harus dikembalikan dalam kondisi baik</li>
                                    <li>• Jika terlambat, akan dikenakan denda per hari</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    {{-- ══ FOOTER ══ --}}
    <footer class="bg-espresso text-paper py-6 mt-12 border-t-2 border-ink">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="font-sans text-[0.7rem] tracking-wide">
                &copy; 2026 Sistem Peminjaman Alat. All Rights Reserved.
            </p>
        </div>
    </footer>

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

        // Validasi tanggal kembali
        const tanggalPeminjamanInput = document.querySelector('input[name="tanggal_peminjaman"]');
        const tanggalKembaliInput = document.querySelector('input[name="tanggal_kembali_rencana"]');

        tanggalPeminjamanInput.addEventListener('change', function() {
            tanggalKembaliInput.min = this.value;
        });
    </script>

</body>
</html>