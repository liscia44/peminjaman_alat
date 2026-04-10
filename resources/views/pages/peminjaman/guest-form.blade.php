@extends('layouts.app')

@section('title', 'Peminjaman Alat - Ajukan Peminjaman')

@section('content')

{{-- ══ PAGE CONTENT ══ --}}
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

    {{-- ══ FORM SECTION ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

                {{-- Nama Guru --}}
                <div class="relative">
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Nama Guru <span class="text-espresso">*</span>
                    </label>
                    <input
                        type="text" 
                        name="nama_peminjam_guest" 
                        value="{{ old('nama_peminjam_guest') }}" 
                        required
                        placeholder="Masukkan nama guru"
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

                {{-- ✅ UPDATED: Jam Pinjam & Jam Kembali (bukan tanggal) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Jam Mulai <span class="text-espresso">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="jam_peminjaman" id="jam_peminjaman_select" required
                                class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
                            >
                                <option value="">Pilih Jam</option>
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

                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Jam Kembali <span class="text-espresso">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="tanggal_kembali_rencana" id="jam_kembali_select" required
                                class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
                            >
                                <option value="">Pilih Jam</option>
                                <option value="07:00 - 08:30">07:00 - 08:30 (Jam 1)</option>
                                <option value="08:30 - 10:00">08:30 - 10:00 (Jam 2)</option>
                                <option value="10:00 - 11:30">10:00 - 11:30 (Jam 3)</option>
                                <option value="11:30 - 13:00">11:30 - 13:00 (Jam 4)</option>
                                <option value="13:00 - 14:30">13:00 - 14:30 (Jam 5)</option>
                                <option value="14:30 - 16:00">14:30 - 16:00 (Jam 6)</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                        </div>
                        @error('tanggal_kembali_rencana')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tujuan --}}
                <div>
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Catatan
                    </label>
                    <textarea
                        name="tujuan_peminjaman" rows="2"
                        placeholder="Catatan tambahan (opsional)..."
                        class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200"
                    >{{ old('tujuan_peminjaman') }}</textarea>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="relative w-full overflow-hidden bg-espresso px-6 py-3.5
                           font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase text-paper
                           flex items-center justify-center gap-2
                           transition-colors duration-200 hover:bg-ink active:scale-[0.99]"
                >
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Ajukan Peminjaman</span>
                </button>

            </form>
        </div>

        {{-- ══ ITEMS LIST (Multiple Scan) ══ --}}
        <div class="bg-paper border border-rule flex flex-col">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-rule">
                <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                    Alat yang Dipinjam
                </p>
                <h3 class="font-serif text-ink text-xl font-normal leading-none">
                    Daftar Unit
                </h3>
            </div>

            {{-- ✅ NEW: Scan Button & List --}}
            <div class="px-6 py-6 flex flex-col gap-4 flex-1 overflow-y-auto">
                {{-- Scan Button --}}
                <button
                    type="button"
                    id="qr_scanner_btn"
                    class="w-full px-4 py-3 bg-cream border border-rule font-sans text-[0.75rem] font-semibold tracking-[0.1em] uppercase text-ink
                           hover:border-espresso hover:bg-espresso/5 transition-all duration-200
                           flex items-center justify-center gap-2"
                >
                    <i class="fas fa-camera text-[0.8rem]"></i>
                    <span>Scan QR Barang</span>
                </button>

                {{-- Items List --}}
                <div id="scanned_items" class="space-y-2">
                    <p class="font-sans text-[0.7rem] text-label text-center py-8">
                        Belum ada barang yang di-scan
                    </p>
                </div>

                {{-- Hidden input untuk item list --}}
                <input type="hidden" name="alat_id" id="alat_id_input">
                <input type="hidden" name="jumlah" value="1">
                <input type="hidden" name="tanggal_peminjaman" id="tanggal_pinjam_input">
            </div>
        </div>
    </div>

</div>

{{-- ══ JAVASCRIPT ══ --}}
<script>
    const qrScannerBtn = document.getElementById('qr_scanner_btn');
    const scannedItems = document.getElementById('scanned_items');
    const alatIdInput = document.getElementById('alat_id_input');
    const tanggalPinjamInput = document.getElementById('tanggal_pinjam_input');

    let video = null;
    let canvas = null;
    let stream = null;
    let isScanning = false;
    let scannedUnits = []; // ✅ NEW: Store scanned units

    // Set tanggal peminjaman ke hari ini
    tanggalPinjamInput.value = new Date().toISOString().split('T')[0];

    qrScannerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        startCamera();
    });

    function startCamera() {
        if (isScanning || video) return;
        
        isScanning = true;

        video = document.createElement('video');
        video.setAttribute('id', 'qr_video');
        video.setAttribute('style', 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;object-fit:cover;');
        document.body.appendChild(video);

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '✕ Tutup Kamera';
        closeBtn.setAttribute('type', 'button');
        closeBtn.setAttribute('style', 'position:fixed;top:20px;right:20px;z-index:10000;padding:10px 20px;background:#1c1917;color:#fffdf9;border:none;cursor:pointer;font-weight:bold;border-radius:5px;font-size:14px;');
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            stopCamera();
        });
        document.body.appendChild(closeBtn);

        canvas = document.createElement('canvas');
        canvas.setAttribute('id', 'qr_canvas');
        canvas.setAttribute('style', 'display:none;');
        document.body.appendChild(canvas);

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        }).then(function(s) {
            stream = s;
            video.srcObject = stream;
            video.setAttribute('autoplay', 'true');
            video.setAttribute('playsinline', 'true');
            video.play();

            setTimeout(() => {
                scanQrCode();
            }, 500);
        }).catch(function(err) {
            console.error('Camera error:', err);
            isScanning = false;
            if (closeBtn) closeBtn.remove();
            alert('Error: ' + err.message);
        });
    }

    function scanQrCode() {
        if (!video || !canvas || !isScanning) return;

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        if (canvas.width === 0 || canvas.height === 0) {
            requestAnimationFrame(scanQrCode);
            return;
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            console.log('QR Detected:', code.data);
            stopCamera();
            processQrData(code.data);
        } else {
            requestAnimationFrame(scanQrCode);
        }
    }

    function processQrData(qrData) {
        console.log('Processing QR Data:', qrData);
        
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
            console.log('Response:', data);
            
            if (data.success) {
                const alat = data.alat;

                // ✅ NEW: Check if already scanned
                const exists = scannedUnits.some(u => u.alat_unit_id === alat.alat_unit_id);
                
                if (exists) {
                    alert('⚠️ Unit ini sudah di-scan!');
                    return;
                }

                // Add to scanned units
                scannedUnits.push(alat);
                
                // Update hidden input (store first item id)
                if (scannedUnits.length === 1) {
                    alatIdInput.value = alat.alat_id;
                }

                renderScannedItems();
                alert(`✓ ${alat.nama_alat} Unit ${alat.unit_number} ditambahkan!`);

            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        });
    }

    function renderScannedItems() {
        if (scannedUnits.length === 0) {
            scannedItems.innerHTML = '<p class="font-sans text-[0.7rem] text-label text-center py-8">Belum ada barang yang di-scan</p>';
            return;
        }

        let html = '';
        scannedUnits.forEach((unit, index) => {
            html += `
                <div class="bg-cream px-3 py-2.5 border border-rule rounded flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="font-sans text-[0.7rem] font-semibold text-ink truncate">
                            ${unit.nama_alat}
                        </p>
                        <p class="font-sans text-[0.62rem] text-label">
                            Unit ${unit.unit_number}
                        </p>
                    </div>
                    <button
                        type="button"
                        onclick="removeItem(${index})"
                        class="ml-2 px-2 py-1 bg-espresso text-paper text-[0.5rem] font-semibold tracking-[0.05em] uppercase
                               hover:bg-ink transition-all flex-shrink-0"
                    >
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            `;
        });

        scannedItems.innerHTML = html;
    }

    function removeItem(index) {
        scannedUnits.splice(index, 1);
        if (scannedUnits.length === 0) {
            alatIdInput.value = '';
        } else {
            alatIdInput.value = scannedUnits[0].alat_id;
        }
        renderScannedItems();
    }

    function stopCamera() {
        isScanning = false;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        if (video) {
            video.remove();
            video = null;
        }
        if (canvas) {
            canvas.remove();
            canvas = null;
        }
        
        const closeBtn = document.querySelector('button[style*="position:fixed"]');
        if (closeBtn) closeBtn.remove();
    }

    window.addEventListener('beforeunload', stopCamera);
</script>

@endsection