@extends('layouts.app')

@section('title', 'Manajemen QR Code')

@section('content')

    <div class="mb-8">
        <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
            Aset
        </p>
        <h2 class="font-serif text-ink text-3xl font-normal leading-none">
            Manajemen QR Code Barang
        </h2>
        <div class="mt-3 h-px w-10 bg-rule"></div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Generate All Button --}}
    <div class="mb-8 flex gap-3">
        <a href="{{ route('qr-generate-all') }}"
            class="relative overflow-hidden inline-flex items-center gap-2 bg-espresso px-5 py-3
                   font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-paper
                   transition-colors duration-200 hover:bg-ink active:scale-[0.99]"
        >
            <i class="fas fa-qrcode text-xs"></i>
            <span>Generate Semua QR Code</span>
        </a>

        <a href="{{ route('qr-download-all-pdf') }}"
            class="relative overflow-hidden inline-flex items-center gap-2 bg-blue-600 px-5 py-3
                   font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-white
                   transition-colors duration-200 hover:bg-blue-700 active:scale-[0.99]"
        >
            <i class="fas fa-download text-xs"></i>
            <span>Download Semua PDF</span>
        </a>
    </div>

    {{-- Daftar Unit QR --}}
    <div class="bg-paper border border-rule">
        <table class="w-full">
            <thead>
                <tr class="border-b border-rule bg-cream">
                    <th class="px-4 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Nama Alat</th>
                    <th class="px-4 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Unit</th>
                    <th class="px-4 py-3.5 text-center font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">QR Code</th>
                    <th class="px-4 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule" id="tableBody">
                @foreach($alatUnits as $unit)
                    <tr class="hover:bg-cream/40" id="row-{{ $unit->id }}">
                        <td class="px-4 py-4 font-sans text-[0.78rem] font-medium text-ink">
                            {{ $unit->alat->nama_alat }}
                        </td>
                        <td class="px-4 py-4 font-sans text-[0.78rem] text-label">
                            Unit {{ $unit->unit_number }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <img id="qr-{{ $unit->id }}" 
                                 src="{{ $unit->qr_code ?? '' }}" 
                                 alt="QR" 
                                 style="width: 80px; height: 80px; {{ !$unit->qr_code ? 'display: none;' : '' }}">
                            <span id="qr-empty-{{ $unit->id }}" 
                                  class="font-sans text-[0.65rem] text-ghost"
                                  style="{{ $unit->qr_code ? 'display: none;' : '' }}">
                                Belum ada
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex gap-2 flex-wrap">
                                {{-- Generate QR --}}
                                <button onclick="generateQr({{ $unit->id }})"
                                    id="btn-generate-{{ $unit->id }}"
                                    class="px-3 py-2 bg-espresso text-paper border border-espresso font-sans text-[0.55rem] font-semibold tracking-[0.1em] uppercase hover:bg-ink transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Generate QR Code">
                                    <i class="fas fa-sync text-xs"></i> Generate
                                </button>

                                {{-- Print --}}
                                <button onclick="printQr('{{ $unit->qr_code ?? '' }}', '{{ $unit->alat->nama_alat }}', 'Unit {{ $unit->unit_number }}')"
                                    id="btn-print-{{ $unit->id }}"
                                    class="px-3 py-2 border border-rule text-label font-sans text-[0.55rem] font-semibold tracking-[0.1em] uppercase hover:border-espresso hover:text-espresso transition-all"
                                    style="{{ !$unit->qr_code ? 'display: none;' : '' }}"
                                    title="Print QR Code">
                                    <i class="fas fa-print text-xs"></i> Print
                                </button>

                                {{-- Download PDF --}}
                                <a href="{{ route('qr-download-pdf', $unit->id) }}"
                                    id="btn-download-{{ $unit->id }}"
                                    class="px-3 py-2 border border-blue-400 text-blue-600 font-sans text-[0.55rem] font-semibold tracking-[0.1em] uppercase hover:bg-blue-50 transition-all"
                                    style="{{ !$unit->qr_code ? 'display: none;' : '' }}"
                                    title="Download as PDF">
                                    <i class="fas fa-download text-xs"></i> PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function generateQr(unitId) {
            const btn = document.getElementById(`btn-generate-${unitId}`);
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Loading...';

            fetch(`{{ url('/qr-generate') }}/${unitId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const img = document.getElementById(`qr-${unitId}`);
                    const empty = document.getElementById(`qr-empty-${unitId}`);
                    const printBtn = document.getElementById(`btn-print-${unitId}`);
                    const downloadBtn = document.getElementById(`btn-download-${unitId}`);

                    img.src = data.qr_code;
                    img.style.display = 'block';
                    empty.style.display = 'none';
                    printBtn.style.display = 'inline-block';
                    if (downloadBtn) downloadBtn.style.display = 'inline-block';

                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync text-xs"></i> Generate';
            });
        }

        function printQr(qrBase64, namaAlat, unitText) {
            if (!qrBase64) {
                alert('QR Code belum tersedia');
                return;
            }

            const printWindow = window.open('', '', 'width=400,height=500');
            const htmlContent = `
                <html>
                    <head>
                        <title>Print QR Code</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                padding: 20px;
                            }
                            .sticker {
                                width: 200px;
                                text-align: center;
                                border: 2px dashed #000;
                                padding: 10px;
                                margin-bottom: 10px;
                            }
                            img { width: 150px; }
                            p { margin: 5px 0; font-size: 12px; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class="sticker">
                            <img src="${qrBase64}" alt="QR Code" />
                            <p>${namaAlat}</p>
                            <p>${unitText}</p>
                        </div>
                    </body>
                </html>
            `;
            
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        function showAlert(type, message) {
            const bgColor = type === 'success' 
                ? 'bg-green-100 border-green-400 text-green-700' 
                : 'bg-red-100 border-red-400 text-red-700';
            
            const alertHtml = `<div class="mb-6 px-4 py-3 ${bgColor} border rounded">${message}</div>`;
            
            const alertDiv = document.createElement('div');
            alertDiv.innerHTML = alertHtml;
            
            const container = document.querySelector('.mb-8');
            if (container) {
                container.insertAdjacentElement('afterend', alertDiv);
            }

            setTimeout(() => alertDiv.remove(), 4000);
        }
    </script>

@endsection