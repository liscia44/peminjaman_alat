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

    {{-- Generate All Button --}}
    <div class="mb-8">
        <a href="{{ route('qr-generate-all') }}"
            class="relative overflow-hidden inline-flex items-center gap-2 bg-espresso px-5 py-3
                   font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-paper
                   transition-colors duration-200 hover:bg-ink active:scale-[0.99]"
        >
            <i class="fas fa-qrcode text-xs"></i>
            <span>Generate Semua QR Code</span>
        </a>
    </div>

    {{-- Daftar Barang & QR --}}
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
            <tbody class="divide-y divide-rule">
                @foreach(\App\Models\Alat::all() as $alat)
                    <tr class="hover:bg-cream/40">
                        <td class="px-4 py-4 font-sans text-[0.78rem] font-medium text-ink">
                            {{ $alat->nama_alat }}
                        </td>
                        <td class="px-4 py-4 font-sans text-[0.78rem] text-label">
                            {{ $alat->nomor_unit ?? '—' }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($alat->qr_code)
                                <img src="{{ $alat->qr_code }}" alt="QR" style="width: 80px; height: 80px;">
                            @else
                                <span class="font-sans text-[0.65rem] text-ghost">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex gap-2">
                                {{-- Generate QR --}}
                                <form action="{{ route('qr-generate', $alat->alat_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-2 bg-espresso text-paper border border-espresso font-sans text-[0.55rem] font-semibold tracking-[0.1em] uppercase hover:bg-ink transition-all">
                                        <i class="fas fa-sync text-xs"></i> Generate
                                    </button>
                                </form>

                                {{-- Print --}}
                                @if($alat->qr_code)
                                    <button onclick="printQr('{{ $alat->qr_code }}', '{{ $alat->nama_alat }}', '{{ $alat->nomor_unit }}')"
                                        class="px-3 py-2 border border-rule text-label font-sans text-[0.55rem] font-semibold tracking-[0.1em] uppercase hover:border-espresso hover:text-espresso transition-all">
                                        <i class="fas fa-print text-xs"></i> Print
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function printQr(qrBase64, namaAlat, nomorUnit) {
            const printWindow = window.open('', '', 'width=400,height=500');
            printWindow.document.write(`
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
                        <img src="${qrBase64}" />
                        <p>${namaAlat}</p>
                        <p>${nomorUnit}</p>
                    </div>
                    <script>
                        window.print();
                        window.close();
                    </script>
                </body>
                </html>
            `);
        }
    </script>

@endsection