@extends('layouts.app')

@section('title', 'Pengembalian Cepat')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="mb-8">
        <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
            Manajemen Aset
        </p>
        <h2 class="font-serif text-ink text-3xl font-normal leading-none">
            Pengembalian Cepat (QR)
        </h2>
        <p class="font-sans text-[0.75rem] text-label mt-2">Scan QR barang untuk pencatatan pengembalian instan</p>
        <div class="mt-3 h-px w-10 bg-rule"></div>
    </div>

    {{-- ══ DEBUG INFO SECTION ══ --}}
    <div id="debug-panel" class="max-w-2xl mx-auto mb-8 bg-cream border border-rule p-4 rounded" style="display: none;">
        <div class="flex items-center justify-between mb-3">
            <p class="font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label">🔍 Debug Info</p>
            <button type="button" onclick="clearDebug()" class="text-xs text-label hover:text-ink">Clear</button>
        </div>
        <div id="debug-messages" style="font-size: 12px; font-family: monospace; max-height: 200px; overflow-y: auto; background: #fff; padding: 8px; border-radius: 4px; border: 1px solid #c8bfb0;"></div>
    </div>

    {{-- ══ SCANNER ══ --}}
    <div class="max-w-2xl mx-auto mb-8">
        <div class="bg-paper border border-rule p-8">
            <button type="button" id="qr_scanner_return"
                class="w-full px-6 py-8 bg-cream border-2 border-dashed border-rule font-sans text-[0.9rem] font-semibold tracking-[0.1em] uppercase text-ink
                    hover:border-espresso hover:bg-espresso/5 transition-all duration-200
                    flex items-center justify-center gap-3 rounded">
                <i class="fas fa-camera text-2xl"></i>
                <span>Buka Kamera - Scan QR Barang</span>
            </button>
            <p id="qr_status_return" class="font-sans text-[0.75rem] text-label mt-4 text-center"></p>
        </div>
    </div>

    {{-- ══ FORM KONDISI (HIDDEN UNTIL SCAN) ══ --}}
    <div id="formKondisi" style="display: none;" class="max-w-2xl mx-auto mb-8">
        <div class="bg-paper border border-rule p-8">

            {{-- Info Barang --}}
            <div class="mb-8 pb-8 border-b border-rule">
                <p class="font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label mb-3">
                    Barang yang Di-Scan
                </p>
                <div class="bg-cream px-4 py-4 rounded">
                    <p id="return_nama_alat" class="font-serif text-[1.1rem] font-normal text-ink mb-1">—</p>
                    <p id="return_nama_peminjam" class="font-sans text-[0.75rem] text-label">—</p>
                    <p id="return_harga" class="font-sans text-[0.75rem] text-label mt-2">Harga: —</p>
                </div>
            </div>

            {{-- KONDISI BUTTONS (3 PILIHAN) ══ --}}
            <div class="mb-8">
                <p class="font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label mb-4">
                    Pilih Kondisi Barang
                </p>
                
                <div class="grid grid-cols-3 gap-4">
                    {{-- BAIK --}}
                    <button type="button" onclick="selectKondisi('baik')" id="btn-baik"
                        class="px-6 py-8 border-2 border-ink/20 bg-ink/5 rounded font-sans text-[0.8rem] font-bold tracking-[0.1em] uppercase
                               hover:border-ink hover:bg-ink/10 transition-all duration-200 flex flex-col items-center gap-3"
                        data-kondisi="baik">
                        <i class="fas fa-check text-2xl text-ink"></i>
                        <span class="text-ink">Baik</span>
                    </button>

                    {{-- RUSAK ══ --}}
                    <button type="button" onclick="selectKondisi('rusak')" id="btn-rusak"
                        class="px-6 py-8 border-2 border-dim/20 bg-dim/5 rounded font-sans text-[0.8rem] font-bold tracking-[0.1em] uppercase
                               hover:border-dim hover:bg-dim/10 transition-all duration-200 flex flex-col items-center gap-3"
                        data-kondisi="rusak">
                        <i class="fas fa-tools text-2xl text-dim"></i>
                        <span class="text-dim">Rusak</span>
                    </button>

                    {{-- HILANG ══ --}}
                    <button type="button" onclick="selectKondisi('hilang')" id="btn-hilang"
                        class="px-6 py-8 border-2 border-espresso/20 bg-espresso/5 rounded font-sans text-[0.8rem] font-bold tracking-[0.1em] uppercase
                               hover:border-espresso hover:bg-espresso/10 transition-all duration-200 flex flex-col items-center gap-3"
                        data-kondisi="hilang">
                        <i class="fas fa-times text-2xl text-espresso"></i>
                        <span class="text-espresso">Hilang</span>
                    </button>
                </div>
            </div>

            {{-- ✅ INPUT CUSTOM PERSENTASE (RUSAK ONLY) ══ --}}
            <div id="persenContainer" style="display: none;" class="mb-8 pb-8 border-b border-rule">
                <label class="block font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label mb-3">
                    Persentase Denda Kerusakan
                </label>
                <div class="relative">
                    <input type="number" id="persen_custom" min="0" max="100" 
                        class="w-full bg-cream border border-rule px-4 py-3 font-sans text-[0.9rem] text-ink outline-none focus:border-dim transition-colors duration-200"
                        placeholder="Masukkan persentase (0-100)">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-sans text-[0.85rem] font-bold text-dim">%</span>
                </div>
                <p id="persenInfo" class="font-sans text-[0.7rem] text-label mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span id="persenDesc">Default: 30%</span>
                </p>
            </div>

            {{-- ══ DENDA PREVIEW ══ --}}
            <div id="dendaPreview" style="display: none;" class="mb-8 pb-8 border-b border-rule bg-cream px-4 py-4 rounded">
                <p class="font-sans text-[0.65rem] font-semibold tracking-[0.2em] uppercase text-label mb-3">
                    💰 Denda yang akan Dikenakan
                </p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="font-sans text-[0.8rem] text-label">Harga Barang:</span>
                        <span id="prev_harga" class="font-sans text-[0.85rem] font-bold text-ink">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center" id="prev_persen_row" style="display: none;">
                        <span class="font-sans text-[0.8rem] text-label">Persentase:</span>
                        <span id="prev_persen" class="font-sans text-[0.85rem] font-bold text-dim">0%</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-rule/50">
                        <span class="font-sans text-[0.9rem] font-bold text-ink">Total Denda:</span>
                        <span id="prev_total_denda" class="font-serif text-[1.2rem] font-bold text-espresso">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- ══ ACTION BUTTONS ══ --}}
            <div class="flex gap-3">
                <button type="button" id="btn_submit_kondisi"
                    class="flex-1 px-6 py-3.5 bg-espresso text-paper font-sans text-[0.7rem] font-bold tracking-[0.1em] uppercase
                           hover:bg-ink transition-colors duration-200 disabled:opacity-50"
                    disabled>
                    <i class="fas fa-check mr-2"></i>
                    Konfirmasi & Lanjut Scan
                </button>
                <button type="button" onclick="resetKondisi()"
                    class="flex-1 px-6 py-3.5 border border-rule text-label font-sans text-[0.7rem] font-bold tracking-[0.1em] uppercase
                           hover:border-espresso hover:text-espresso transition-all duration-200">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- ══ DAFTAR PENGEMBALIAN ══ --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-paper border border-rule">
            <div class="px-6 py-4 border-b border-rule bg-cream">
                <h3 class="font-serif text-ink text-lg font-normal">Barang yang Dikembalikan Hari Ini</h3>
                <p id="countBarang" class="font-sans text-[0.7rem] text-label mt-1">0 barang</p>
            </div>
            <div id="daftarBarangKembali" class="divide-y divide-rule">
                <p class="px-6 py-6 font-sans text-[0.7rem] text-label text-center">Belum ada barang yang dikembalikan</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    let currentScannedAlat = null;
    let selectedKondisi = null;
    let scannedList = [];

    // ✅ QR SCANNER VARIABLES
    let video = null;
    let canvas = null;
    let stream = null;
    let isScanning = false;
    let cameraBusy = false;

    // ✅ DEBUG FUNCTION
    function addDebug(message) {
        const debugPanel = document.getElementById('debug-panel');
        const debugMessages = document.getElementById('debug-messages');
        
        const timestamp = new Date().toLocaleTimeString('id-ID');
        const line = document.createElement('div');
        line.textContent = `[${timestamp}] ${message}`;
        debugMessages.appendChild(line);
        
        debugPanel.style.display = 'block';
        debugPanel.scrollTop = debugPanel.scrollHeight;
        
        console.log(`[${timestamp}]`, message);
    }

    function clearDebug() {
        document.getElementById('debug-messages').innerHTML = '';
    }

    function selectKondisi(kondisi) {
        selectedKondisi = kondisi;
        addDebug(`✅ Kondisi dipilih: ${kondisi}`);

        // Update button states
        ['baik', 'rusak', 'hilang'].forEach(k => {
            const btn = document.getElementById(`btn-${k}`);
            if (k === kondisi) {
                btn.classList.add('ring-2', 'ring-offset-2');
            } else {
                btn.classList.remove('ring-2', 'ring-offset-2');
            }
        });

        // Show persentase input kalau rusak
        const persenContainer = document.getElementById('persenContainer');
        const persen_input = document.getElementById('persen_custom');
        
        if (kondisi === 'rusak') {
            persenContainer.style.display = 'block';
            persen_input.value = currentScannedAlat?.persen_default_rusak || 30;
            updateDendaPreview();
            persen_input.addEventListener('input', updateDendaPreview);
        } else {
            persenContainer.style.display = 'none';
            updateDendaPreview();
        }

        document.getElementById('btn_submit_kondisi').disabled = false;
    }

    function updateDendaPreview() {
        if (!currentScannedAlat || !selectedKondisi) return;

        const harga = currentScannedAlat.harga_alat;
        let denda = 0;
        let showPersen = false;

        if (selectedKondisi === 'baik') {
            denda = 0;
        } else if (selectedKondisi === 'rusak') {
            const persen = parseInt(document.getElementById('persen_custom').value) || 30;
            denda = (harga * (persen / 100)) * currentScannedAlat.jumlah;
            document.getElementById('prev_persen').textContent = persen + '%';
            showPersen = true;
        } else if (selectedKondisi === 'hilang') {
            denda = harga * currentScannedAlat.jumlah;
        }

        // Update preview
        document.getElementById('prev_harga').textContent = 'Rp ' + formatCurrency(harga);
        document.getElementById('prev_total_denda').textContent = 'Rp ' + formatCurrency(denda);
        document.getElementById('prev_persen_row').style.display = showPersen ? 'flex' : 'none';
        
        if (denda > 0) {
            document.getElementById('dendaPreview').style.display = 'block';
        } else {
            document.getElementById('dendaPreview').style.display = 'none';
        }
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }

    function resetKondisi() {
        selectedKondisi = null;
        ['baik', 'rusak', 'hilang'].forEach(k => {
            document.getElementById(`btn-${k}`).classList.remove('ring-2', 'ring-offset-2');
        });
        document.getElementById('btn_submit_kondisi').disabled = true;
        document.getElementById('formKondisi').style.display = 'none';
        document.getElementById('persenContainer').style.display = 'none';
        document.getElementById('dendaPreview').style.display = 'none';
        addDebug('🔄 Form di-reset');
    }

    // ✅ QR SCANNER - START CAMERA
    document.getElementById('qr_scanner_return').addEventListener('click', function(e) {
        e.preventDefault();
        if (!cameraBusy) {
            cameraBusy = true;
            addDebug('🎥 Membuka kamera...');
            startReturnCamera();
            setTimeout(() => { cameraBusy = false; }, 500);
        }
    });

    function startReturnCamera() {
        if (isScanning || video) {
            addDebug('⚠️ Kamera sudah berjalan');
            return;
        }
        
        isScanning = true;
        cleanupCamera();

        // Create video element
        video = document.createElement('video');
        video.id = 'qr_video_return';
        video.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;object-fit:cover;';
        document.body.appendChild(video);

        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.innerHTML = '✕ Tutup Kamera';
        closeBtn.className = 'qr-close-btn';
        closeBtn.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;padding:10px 20px;background:#1c1917;color:#fffdf9;border:none;cursor:pointer;font-weight:bold;border-radius:5px;font-family:Arial,sans-serif;';
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            addDebug('❌ Kamera ditutup user');
            stopReturnCamera();
        });
        document.body.appendChild(closeBtn);

        // Create canvas
        canvas = document.createElement('canvas');
        canvas.id = 'qr_canvas_return';
        canvas.style.display = 'none';
        document.body.appendChild(canvas);

        // Request camera dengan timeout
        const cameraTimeout = setTimeout(() => {
            addDebug('❌ Timeout: Kamera tidak merespons');
            document.getElementById('qr_status_return').textContent = '❌ Kamera timeout. Coba lagi.';
            document.getElementById('qr_status_return').style.color = '#b23d3d';
            stopReturnCamera();
        }, 10000);

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        })
        .then(s => {
            clearTimeout(cameraTimeout);
            addDebug('✅ Stream kamera diperoleh');
            stream = s;
            video.srcObject = stream;
            video.setAttribute('autoplay', 'true');
            video.setAttribute('playsinline', 'true');
            
            video.onloadedmetadata = () => {
                video.play();
                addDebug('✅ Video sedang diputar');
                
                document.getElementById('qr_status_return').textContent = '📹 Arahkan kamera ke QR code...';
                document.getElementById('qr_status_return').style.color = '#1c1917';
                
                scanQrCodeReturn();
            };
        })
        .catch(err => {
            clearTimeout(cameraTimeout);
            addDebug(`❌ Error Kamera: ${err.message}`);
            document.getElementById('qr_status_return').textContent = '❌ Error: ' + err.message;
            document.getElementById('qr_status_return').style.color = '#b23d3d';
            stopReturnCamera();
        });
    }

    // ✅ SCAN QR CODE
    function scanQrCodeReturn() {
        if (!video || !canvas || !isScanning) {
            return;
        }

        const ctx = canvas.getContext('2d');
        
        if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth && video.videoHeight) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            try {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);

                if (code) {
                    addDebug(`✅ QR terdeteksi: ${code.data.substring(0, 50)}...`);
                    stopReturnCamera();
                    processQrDataReturn(code.data);
                    return;
                }
            } catch (e) {
                addDebug(`❌ Canvas Error: ${e.message}`);
            }
        }

        requestAnimationFrame(scanQrCodeReturn);
    }

    // ✅ PROCESS QR DATA - FIXED!
    function processQrDataReturn(qrData) {
        addDebug('📤 Memproses QR data...');
        
        // ✅ FIX: Jangan stringify 2x
        // qrData sudah string dari jsQR, langsung kirim
        let qrPayload;
        
        try {
            // Validasi bahwa JSON valid
            JSON.parse(qrData);
            qrPayload = qrData; // ✅ PENTING: kirim string asli, BUKAN stringify lagi
            addDebug('✅ JSON valid: akan dikirim ke server');
        } catch (e) {
            addDebug(`❌ JSON invalid: ${e.message}`);
            document.getElementById('qr_status_return').textContent = '❌ Format QR tidak valid';
            document.getElementById('qr_status_return').style.color = '#b23d3d';
            return;
        }

        // ✅ KIRIM PAYLOAD
        fetch('/pengembalian/api/get-from-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qr_data: qrPayload })
        })
        .then(r => {
            addDebug(`📡 HTTP Status: ${r.status}`);
            
            if (!r.ok) {
                throw new Error(`HTTP ${r.status}: ${r.statusText}`);
            }
            
            // ✅ Cek content-type
            const contentType = r.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Invalid content-type: ${contentType}`);
            }
            
            return r.json();
        })
        .then(data => {
            addDebug(`✅ Response diterima: ${JSON.stringify(data).substring(0, 100)}...`);
            
            if (data.success) {
                const alat = data.alat;
                currentScannedAlat = alat;
                
                addDebug(`✅ Barang terdeteksi: ${alat.nama_alat} Unit ${alat.unit_number}`);
                
                document.getElementById('formKondisi').style.display = 'block';
                document.getElementById('return_nama_alat').textContent = alat.nama_alat + ' (Unit ' + alat.unit_number + ')';
                document.getElementById('return_nama_peminjam').textContent = 'Peminjam: ' + alat.nama_peminjam;
                document.getElementById('return_harga').textContent = 'Harga: Rp ' + formatCurrency(alat.harga_alat);
                
                document.getElementById('qr_status_return').textContent = '✅ Barang terdeteksi! Pilih kondisi barang.';
                document.getElementById('qr_status_return').style.color = '#1c1917';
            } else {
                addDebug(`❌ Error dari server: ${data.message}`);
                document.getElementById('qr_status_return').textContent = '❌ ' + (data.message || 'Alat tidak ditemukan');
                document.getElementById('qr_status_return').style.color = '#b23d3d';
            }
        })
        .catch(error => {
            addDebug(`❌ Fetch Error: ${error.message}`);
            document.getElementById('qr_status_return').textContent = '❌ Error: ' + error.message;
            document.getElementById('qr_status_return').style.color = '#b23d3d';
        });
    }

    // ✅ SUBMIT KONDISI
    document.getElementById('btn_submit_kondisi').addEventListener('click', function() {
        if (!currentScannedAlat || !selectedKondisi) return;

        const persen = selectedKondisi === 'rusak' 
            ? parseInt(document.getElementById('persen_custom').value) || 30
            : (selectedKondisi === 'hilang' ? 100 : 0);

        const payload = {
            peminjaman_id: currentScannedAlat.peminjaman_id,
            alat_unit_id: currentScannedAlat.alat_unit_id,
            alat_id: currentScannedAlat.alat_id,
            kondisi: selectedKondisi,
            persen_denda_custom: persen,
            tanggal_kembali: new Date().toISOString().split('T')[0],
        };

        addDebug(`📤 Mengirim: ${JSON.stringify(payload)}`);

        fetch('/pengembalian/quick-process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        })
        .then(r => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
        })
        .then(data => {
            if (data.success) {
                addDebug(`✅ Pengembalian berhasil! Denda: Rp ${formatCurrency(data.denda)}`);
                
                // Add to list
                scannedList.push({
                    nama: currentScannedAlat.nama_alat,
                    peminjam: currentScannedAlat.nama_peminjam,
                    kondisi: selectedKondisi,
                    denda: data.denda,
                    unit: currentScannedAlat.unit_number,
                });

                // Update UI
                document.getElementById('countBarang').textContent = scannedList.length + ' barang';
                updateBarangList();

                // Reset
                resetKondisi();
                currentScannedAlat = null;
                selectedKondisi = null;
                document.getElementById('qr_status_return').textContent = '✅ Berhasil! Siap scan barang berikutnya.';
                document.getElementById('qr_status_return').style.color = '#1c1917';
            } else {
                addDebug(`❌ Server error: ${data.message}`);
                alert('Error: ' + (data.message || 'Gagal memproses pengembalian'));
            }
        })
        .catch(err => {
            addDebug(`❌ Fetch Error: ${err.message}`);
            alert('Error: ' + err.message);
        });
    });

    function updateBarangList() {
        const list = document.getElementById('daftarBarangKembali');
        list.innerHTML = scannedList.map((item, i) => `
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex-1">
                    <p class="font-sans text-[0.8rem] font-semibold text-ink">${item.nama} (Unit ${item.unit})</p>
                    <p class="font-sans text-[0.7rem] text-label">${item.peminjam}</p>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 border border-rule/50 bg-cream font-sans text-[0.65rem] font-bold tracking-[0.1em] uppercase">
                        ${item.kondisi === 'baik' ? '✓ Baik' : item.kondisi === 'rusak' ? '⚠️ Rusak' : '❌ Hilang'}
                    </span>
                    <p class="font-sans text-[0.75rem] font-bold text-espresso mt-1">Rp ${formatCurrency(item.denda)}</p>
                </div>
            </div>
        `).join('');
    }

    // ✅ CLEANUP CAMERA
    function cleanupCamera() {
        addDebug('🧹 Membersihkan resource kamera...');
        
        if (stream) {
            stream.getTracks().forEach(track => {
                track.stop();
            });
            stream = null;
        }
        
        const existingVideo = document.getElementById('qr_video_return');
        if (existingVideo) existingVideo.remove();
        
        const existingCanvas = document.getElementById('qr_canvas_return');
        if (existingCanvas) existingCanvas.remove();
        
        const closeBtns = document.querySelectorAll('.qr-close-btn');
        closeBtns.forEach(btn => btn.remove());

        video = null;
        canvas = null;
    }

    // ✅ STOP CAMERA
    function stopReturnCamera() {
        addDebug('⏹️ Menghentikan kamera');
        isScanning = false;
        cleanupCamera();
    }

    // ✅ CLEANUP ON PAGE UNLOAD
    window.addEventListener('beforeunload', stopReturnCamera);

    addDebug('✅ Quick Return Script initialized');
</script>

@endsection