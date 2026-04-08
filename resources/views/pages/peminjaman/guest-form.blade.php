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

                            {{-- QR Scanner Input --}}
                            <div>
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Scan QR Barang <span class="text-espresso">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text" 
                                        id="qr_scanner_input" 
                                        placeholder="Arahkan kamera ke QR code barang"
                                        class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                                        autocomplete="off"
                                    >
                                    <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                                    <i class="fas fa-camera absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.8rem]"></i>
                                </div>
                                <p id="qr_status" class="font-sans text-[0.62rem] text-label mt-1.5"></p>
                            </div>

                            {{-- Hidden input untuk alat_id --}}
                            <input type="hidden" name="alat_id" id="alat_id_input">

                            {{-- Display Alat yang ter-scan --}}
                            <div id="alat_terpilih" style="display: none;" class="bg-cream px-4 py-3 border border-rule rounded mb-4">
                                <p class="font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label mb-2">
                                    Barang Terpilih ✓
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="font-sans text-[0.55rem] text-ghost mb-0.5">Nama</p>
                                        <p id="alat_nama" class="font-sans text-[0.8rem] font-semibold text-ink">—</p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-[0.55rem] text-ghost mb-0.5">Unit</p>
                                        <p id="alat_unit" class="font-sans text-[0.8rem] font-semibold text-ink">—</p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-[0.55rem] text-ghost mb-0.5">Stok</p>
                                        <p id="alat_stok" class="font-sans text-[0.8rem] font-semibold text-ink">—</p>
                                    </div>
                                    <div>
                                        <p class="font-sans text-[0.55rem] text-ghost mb-0.5">Harga</p>
                                        <p id="alat_harga" class="font-sans text-[0.8rem] font-semibold text-ink">—</p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearQrScan()" class="mt-3 w-full text-center border border-rule text-label px-3 py-2 font-sans text-[0.65rem] font-semibold tracking-[0.1em] uppercase hover:border-espresso hover:text-espresso transition-all">
                                    Scan Ulang
                                </button>
                            </div>

                            {{-- Jumlah (auto-fill ke 1) --}}
                            <div class="relative">
                                <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                    Jumlah <span class="text-espresso">*</span>
                                </label>
                                <input
                                    type="number" id="jumlah_input" name="jumlah" min="1" required value="1"
                                    placeholder="Jumlah unit yang dipinjam"
                                    class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                                >
                                <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                                <p id="stok_info" class="font-sans text-[0.62rem] text-label mt-1.5"></p>
                                @error('jumlah')
                                    <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
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
                                        class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
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
                                        class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
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
                                        class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
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

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>


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




        const qrScannerInput = document.getElementById('qr_scanner_input');
    const qrStatus = document.getElementById('qr_status');
    const alatTerpilih = document.getElementById('alat_terpilih');
    const alatIdInput = document.getElementById('alat_id_input');

    // Buka camera saat input di-focus
    qrScannerInput.addEventListener('focus', startCamera);

    let video = null;
    let canvas = null;
    let stream = null;

    function startCamera() {
        if (video) return; // Camera sudah buka

        // Buat video element
        video = document.createElement('video');
        video.setAttribute('id', 'qr_video');
        video.setAttribute('style', 'display:none;');
        document.body.appendChild(video);

        // Buat canvas element
        canvas = document.createElement('canvas');
        canvas.setAttribute('id', 'qr_canvas');
        canvas.setAttribute('style', 'display:none;');
        document.body.appendChild(canvas);

        // Minta akses camera
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment' // Gunakan rear camera
            } 
        }).then(function(s) {
            stream = s;
            video.srcObject = stream;
            video.play();

            qrStatus.textContent = '📹 Camera aktif - arahkan ke QR code';
            qrStatus.style.color = '#1c1917';

            // Scan QR code
            scanQrCode();
        }).catch(function(err) {
            qrStatus.textContent = '❌ Error: Izin camera ditolak';
            qrStatus.style.color = '#b23d3d';
        });
    }

    function scanQrCode() {
        if (!video || !canvas) return;

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            // QR terdeteksi!
            stopCamera();
            processQrData(code.data);
        } else {
            // Scan lagi
            requestAnimationFrame(scanQrCode);
        }
    }

    function processQrData(qrData) {
        // Send ke backend untuk validate
        fetch('/api/scan-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                qr_data: qrData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display barang yang ter-scan
                document.getElementById('alat_nama').textContent = data.alat.nama_alat;
                document.getElementById('alat_unit').textContent = data.alat.nomor_unit || '—';
                document.getElementById('alat_stok').textContent = data.alat.stok_tersedia + ' unit';
                document.getElementById('alat_harga').textContent = 'Rp ' + formatCurrency(data.alat.harga_alat);

                alatIdInput.value = data.alat.alat_id;
                alatTerpilih.style.display = 'block';

                qrStatus.textContent = '✓ Barang terdeteksi!';
                qrStatus.style.color = '#1c1917';

                // Update jumlah maksimal
                document.getElementById('jumlah_input').max = data.alat.stok_tersedia;
                document.getElementById('stok_info').textContent = 'Maksimal: ' + data.alat.stok_tersedia + ' unit tersedia';

            } else {
                qrStatus.textContent = '❌ ' + data.message;
                qrStatus.style.color = '#b23d3d';
                startCamera();
            }
        })
        .catch(error => {
            qrStatus.textContent = '❌ Error scanning';
            qrStatus.style.color = '#b23d3d';
            startCamera();
        });
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function clearQrScan() {
        alatIdInput.value = '';
        alatTerpilih.style.display = 'none';
        qrStatus.textContent = '';
        qrScannerInput.value = '';
        stopCamera();
        video = null;
        canvas = null;
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }

    // Cleanup saat halaman ditutup
    window.addEventListener('beforeunload', stopCamera);
    </script>

</body>
</html>