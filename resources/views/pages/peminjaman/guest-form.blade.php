<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Alat - Formulir Pengajuan</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-ghost/10">

    {{-- Navigation Bar --}}
    <nav class="bg-paper border-b border-rule sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-espresso flex items-center justify-center">
                    <i class="fas fa-toolbox text-paper text-lg"></i>
                </div>
                <div>
                    <h1 class="font-serif text-ink font-normal text-lg">Sistem Peminjaman</h1>
                    <p class="font-sans text-[0.65rem] text-label tracking-wide">Alat & Peralatan</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-ink text-paper font-sans text-[0.75rem] font-semibold tracking-wide hover:bg-espresso transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-sign-in-alt"></i> LOGIN ADMIN
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-6xl mx-auto px-6 py-12">

        {{-- Page Header --}}
        <div class="mb-12">
            <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-2">
                Selamat Datang
            </p>
            <h2 class="font-serif text-ink text-4xl font-normal leading-tight mb-4">
                Ajukan Peminjaman Alat
            </h2>
            <p class="font-sans text-[0.85rem] text-label leading-relaxed max-w-2xl">
                Isi formulir berikut untuk mengajukan peminjaman alat. Anda akan menerima kode peminjaman yang dapat digunakan untuk tracking.
            </p>
            <div class="mt-4 h-px w-16 bg-espresso"></div>
        </div>

        {{-- Success Alert dengan Kode Peminjaman --}}
        @if(session('success'))
            <div class="mb-8 border-l-4 border-ink bg-cream p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-ink text-2xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-serif text-ink text-lg font-normal mb-2">Peminjaman Berhasil!</h3>
                        <p class="font-sans text-[0.8rem] text-label mb-3">
                            {!! str_replace('<strong>', '<strong class="text-ink font-semibold">', session('success')) !!}
                        </p>
                        <div class="mt-4 p-4 bg-ink/5 border border-ink/20">
                            <p class="font-sans text-[0.7rem] text-label mb-2">Kode Peminjaman Anda:</p>
                            <p class="font-mono text-lg font-bold text-ink tracking-widest">{{ session('kode_peminjaman') }}</p>
                            <p class="font-sans text-[0.7rem] text-label mt-2">Simpan kode ini untuk referensi tracking Anda</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Error Alert --}}
        @if($errors->any())
            <div class="mb-8 border-l-4 border-espresso bg-espresso/5 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-espresso text-2xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-serif text-espresso text-lg font-normal mb-3">Terjadi Kesalahan!</h3>
                        <ul class="font-sans text-[0.8rem] text-espresso space-y-2">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Form (2/3 width) --}}
            <div class="lg:col-span-2">
                <div class="bg-paper border border-rule">

                    {{-- Form Header --}}
                    <div class="px-8 py-6 border-b border-rule bg-cream/50">
                        <h3 class="font-serif text-ink text-2xl font-normal">
                            Formulir Peminjaman
                        </h3>
                        <p class="font-sans text-[0.75rem] text-label tracking-wide mt-2">
                            Semua field dengan tanda <span class="text-espresso font-bold">*</span> harus diisi
                        </p>
                    </div>

                    {{-- Form Body --}}
                    <form action="{{ route('peminjaman.guest.store') }}" method="POST" class="px-8 py-8 space-y-8">
                        @csrf

                        {{-- SECTION 1: Data Pribadi --}}
                        <div>
                            <h4 class="font-serif text-ink text-lg font-normal mb-6 pb-3 border-b border-rule">
                                <i class="fas fa-user text-espresso mr-2"></i> Data Pribadi
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama Lengkap --}}
                                <div>
                                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                        Nama Lengkap <span class="text-espresso">*</span>
                                    </label>
                                    <input
                                        type="text" 
                                        name="nama_peminjam_guest" 
                                        value="{{ old('nama_peminjam_guest') }}" 
                                        required
                                        placeholder="Contoh: Budi Santoso"
                                        class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/50 focus:border-ink focus:bg-white transition-colors duration-200"
                                    >
                                    @error('nama_peminjam_guest')
                                        <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Telepon --}}
                                <div>
                                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                        Nomor Telepon <span class="text-espresso">*</span>
                                    </label>
                                    <input
                                        type="tel" 
                                        name="telepon_peminjam_guest" 
                                        value="{{ old('telepon_peminjam_guest') }}" 
                                        required
                                        placeholder="+62 8xx xxx xxxx"
                                        class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/50 focus:border-ink focus:bg-white transition-colors duration-200"
                                    >
                                    @error('telepon_peminjam_guest')
                                        <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: Detail Peminjaman --}}
                        <div>
                            <h4 class="font-serif text-ink text-lg font-normal mb-6 pb-3 border-b border-rule">
                                <i class="fas fa-box text-espresso mr-2"></i> Detail Peminjaman
                            </h4>

                            {{-- Pilih Alat --}}
                            <div class="mb-6">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                    Pilih Alat <span class="text-espresso">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        name="alat_id" 
                                        id="alat_select" 
                                        required
                                        class="w-full appearance-none bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none focus:border-ink focus:bg-white transition-colors duration-200"
                                    >
                                        <option value="">-- Pilih Alat --</option>
                                        @foreach($alats as $alat)
                                            <option value="{{ $alat->alat_id }}"
                                                data-max="{{ $alat->stok_tersedia }}"
                                                {{ old('alat_id') == $alat->alat_id ? 'selected' : '' }}>
                                                {{ $alat->nama_alat }} (Tersedia: {{ $alat->stok_tersedia }} unit)
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-ghost pointer-events-none"></i>
                                </div>
                                @error('alat_id')
                                    <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Jumlah --}}
                            <div class="mb-6">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                    Jumlah Unit <span class="text-espresso">*</span>
                                </label>
                                <div class="flex items-end gap-4">
                                    <div class="flex-1">
                                        <input
                                            type="number" 
                                            id="jumlah_input" 
                                            name="jumlah" 
                                            value="{{ old('jumlah', 1) }}" 
                                            min="1" 
                                            required
                                            placeholder="Jumlah yang dipinjam"
                                            class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/50 focus:border-ink focus:bg-white transition-colors duration-200"
                                        >
                                    </div>
                                    <p id="stok_info" class="font-sans text-[0.65rem] text-label whitespace-nowrap pb-3 font-medium">
                                        Pilih alat terlebih dahulu
                                    </p>
                                </div>
                                @error('jumlah')
                                    <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal Peminjaman & Kembali --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                        Tanggal Peminjaman <span class="text-espresso">*</span>
                                    </label>
                                    <input
                                        type="date" 
                                        name="tanggal_peminjaman" 
                                        value="{{ old('tanggal_peminjaman') }}" 
                                        required
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none focus:border-ink focus:bg-white transition-colors duration-200"
                                    >
                                    @error('tanggal_peminjaman')
                                        <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                        Tanggal Kembali <span class="text-espresso">*</span>
                                    </label>
                                    <input
                                        type="date" 
                                        name="tanggal_kembali_rencana" 
                                        value="{{ old('tanggal_kembali_rencana') }}" 
                                        required
                                        class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none focus:border-ink focus:bg-white transition-colors duration-200"
                                    >
                                    @error('tanggal_kembali_rencana')
                                        <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 3: Keterangan --}}
                        <div>
                            <h4 class="font-serif text-ink text-lg font-normal mb-6 pb-3 border-b border-rule">
                                <i class="fas fa-notes-medical text-espresso mr-2"></i> Keterangan
                            </h4>

                            <div>
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-3">
                                    Tujuan Peminjaman
                                </label>
                                <textarea
                                    name="tujuan_peminjaman" 
                                    rows="5"
                                    placeholder="Jelaskan untuk keperluan apa alat ini akan digunakan (misalnya: Proyek renovasi ruang kelas, Kegiatan olahraga, dll)..."
                                    class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/50 focus:border-ink focus:bg-white transition-colors duration-200 resize-none"
                                >{{ old('tujuan_peminjaman') }}</textarea>
                                @error('tujuan_peminjaman')
                                    <p class="font-sans text-[0.7rem] text-espresso mt-2 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-xs"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="pt-6 flex gap-3 border-t border-rule">
                            <button
                                type="submit"
                                class="flex-1 bg-espresso hover:bg-ink text-paper px-6 py-4 font-sans text-[0.75rem] font-semibold tracking-[0.2em] uppercase transition-colors duration-200 active:scale-95 flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                            <button
                                type="reset"
                                class="px-6 py-4 bg-ghost/20 text-ink font-sans text-[0.75rem] font-semibold tracking-[0.2em] uppercase hover:bg-ghost/30 transition-colors duration-200 flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Sidebar (1/3 width) --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Info Box 1 --}}
                <div class="bg-cream border border-rule p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-8 h-8 bg-ink text-paper flex items-center justify-center flex-shrink-0 rounded-full">
                            <i class="fas fa-info text-xs"></i>
                        </div>
                        <h4 class="font-serif text-ink font-normal text-base">Informasi Penting</h4>
                    </div>
                    <ul class="font-sans text-[0.75rem] text-label leading-relaxed space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-espresso mt-1">✓</span>
                            <span>Data Anda akan diverifikasi oleh admin</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-espresso mt-1">✓</span>
                            <span>Anda akan menerima kode peminjaman</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-espresso mt-1">✓</span>
                            <span>Simpan kode untuk tracking</span>
                        </li>
                    </ul>
                </div>

                {{-- Info Box 2 --}}
                <div class="bg-cream border border-rule p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-8 h-8 bg-ink text-paper flex items-center justify-center flex-shrink-0 rounded-full">
                            <i class="fas fa-clock text-xs"></i>
                        </div>
                        <h4 class="font-serif text-ink font-normal text-base">Waktu Proses</h4>
                    </div>
                    <p class="font-sans text-[0.75rem] text-label leading-relaxed">
                        Permohonan Anda akan diproses dalam waktu <strong class="text-ink font-semibold">1-2 hari kerja</strong> oleh bagian admin.
                    </p>
                </div>

                {{-- Info Box 3 --}}
                <div class="bg-ink text-paper p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-8 h-8 bg-paper text-ink flex items-center justify-center flex-shrink-0 rounded-full">
                            <i class="fas fa-lock text-xs"></i>
                        </div>
                        <h4 class="font-serif text-paper font-normal text-base">Admin & Petugas?</h4>
                    </div>
                    <p class="font-sans text-[0.75rem] text-paper/85 leading-relaxed mb-4">
                        Jika Anda adalah admin atau petugas, gunakan tombol login di atas untuk mengakses panel administrasi.
                    </p>
                    <a href="{{ route('login') }}" class="inline-block px-4 py-3 bg-paper text-ink font-sans text-[0.7rem] font-semibold tracking-wide hover:bg-cream transition-colors duration-200 w-full text-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> MASUK PANEL ADMIN
                    </a>
                </div>

                {{-- FAQ Box --}}
                <div class="bg-cream border border-rule p-6">
                    <h4 class="font-serif text-ink font-normal text-base mb-4 flex items-center gap-2">
                        <i class="fas fa-question-circle text-espresso"></i> FAQ
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="font-sans text-[0.7rem] font-semibold text-ink mb-1">Berapa lama durasi peminjaman?</p>
                            <p class="font-sans text-[0.7rem] text-label">Durasi peminjaman sesuai dengan tanggal yang Anda tentukan di form</p>
                        </div>
                        <div>
                            <p class="font-sans text-[0.7rem] font-semibold text-ink mb-1">Apa itu kode peminjaman?</p>
                            <p class="font-sans text-[0.7rem] text-label">Kode unik untuk tracking status peminjaman Anda</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    {{-- Footer --}}
    <footer class="bg-ink text-paper mt-16 py-8">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <p class="font-sans text-[0.75rem] tracking-wide">
                &copy; 2026 Sistem Peminjaman Alat. All Rights Reserved.
            </p>
        </div>
    </footer>

    <script>
        const alatSelect = document.getElementById('alat_select');
        const jumlahInput = document.getElementById('jumlah_input');
        const stokInfo = document.getElementById('stok_info');
        const tanggalPeminjamanInput = document.querySelector('input[name="tanggal_peminjaman"]');
        const tanggalKembaliInput = document.querySelector('input[name="tanggal_kembali_rencana"]');

        // Update stok info saat alat dipilih
        alatSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const maxStok = selected.getAttribute('data-max');

            if (maxStok) {
                jumlahInput.max = maxStok;
                jumlahInput.value = 1;
                stokInfo.textContent = 'Max: ' + maxStok + ' unit tersedia';
                stokInfo.classList.remove('text-label');
                stokInfo.classList.add('text-ink', 'font-semibold');
            } else {
                jumlahInput.max = '';
                jumlahInput.value = 1;
                stokInfo.textContent = 'Pilih alat terlebih dahulu';
                stokInfo.classList.remove('text-ink', 'font-semibold');
                stokInfo.classList.add('text-label');
            }
        });

        // Validasi tanggal kembali harus lebih besar dari tanggal peminjaman
        tanggalPeminjamanInput.addEventListener('change', function() {
            tanggalKembaliInput.min = this.value;
        });
    </script>

</body>
</html>