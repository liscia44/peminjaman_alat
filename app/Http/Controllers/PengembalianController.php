<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use App\Models\AlatUnit;   // ← TAMBAH INI
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalian = Pengembalian::with('peminjaman.user', 'peminjaman.alat', 'details')->latest()->get();
        
        $totalDenda = $pengembalian->sum('total_denda');
        $dendaBelumLunas = $pengembalian->where('status_denda', 'belum_lunas')->sum('total_denda');
        
        $alatRusak = PengembalianDetail::where('kondisi_alat', 'rusak')->sum('jumlah');
        $alatHilang = PengembalianDetail::where('kondisi_alat', 'hilang')->sum('jumlah');
        
        return view('pages.pengembalian.index', compact(
            'pengembalian',
            'totalDenda',
            'dendaBelumLunas',
            'alatRusak',
            'alatHilang'
        ));
    }

   public function store(Request $request)
{
    // ✅ Validasi sama seperti sebelumnya
    $validated = $request->validate([
        'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
        'tanggal_kembali_aktual' => 'required|date',
        'kondisi_details' => 'required|array',
        'kondisi_details.*.kondisi' => 'required|in:baik,rusak,hilang',
        'kondisi_details.*.jumlah' => 'required|integer|min:0',
        'keterangan' => 'nullable|string',
    ]);

    $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
    $alat = $peminjaman->alat;

    // ✅ Filter out items dengan jumlah = 0
    $kondisiDetails = array_filter($validated['kondisi_details'], function($item) {
        return $item['jumlah'] > 0;
    });

    $kondisiDetails = array_values($kondisiDetails);

    if (empty($kondisiDetails)) {
        return redirect()->back()
            ->withErrors(['kondisi_details' => "Minimal ada 1 barang yang dikembalikan"])
            ->withInput();
    }

    // ✅ Validasi total jumlah
    $totalJumlahDikembalikan = 0;
    foreach ($kondisiDetails as $detail) {
        $totalJumlahDikembalikan += $detail['jumlah'];
    }

    if ($totalJumlahDikembalikan != $peminjaman->jumlah) {
        return redirect()->back()
            ->withErrors(['kondisi_details' => "Total jumlah barang yang dikembalikan harus {$peminjaman->jumlah}. Anda memasukkan {$totalJumlahDikembalikan}"])
            ->withInput();
    }

    // ✅ HAPUS perhitungan keterlambatan, langsung ke denda barang
    $persenDendaRusak = $alat->persen_denda_rusak ?? 30;

    DB::transaction(function () use (
        $validated,
        $kondisiDetails,
        $peminjaman,
        $alat,
        $persenDendaRusak
    ) {
        // ✅ Buat pengembalian TANPA denda keterlambatan
        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $validated['peminjaman_id'],
            'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            'total_denda' => 0,
            'status_denda' => 'belum_lunas',
            'keterangan' => $validated['keterangan'],
        ]);

        $totalDendaBarang = 0;
        $jumlahBaik = 0;
        $jumlahRusak = 0;
        $jumlahHilang = 0;

        // ✅ Hitung denda HANYA dari rusak & hilang
        foreach ($kondisiDetails as $detail) {
            $kondisi = $detail['kondisi'];
            $jumlah = $detail['jumlah'];
            $dendaDetail = 0;

            if ($kondisi == 'baik') {
                $dendaDetail = 0;
                $jumlahBaik += $jumlah;
            } elseif ($kondisi == 'rusak') {
                // Rusak: charge harga_alat × persen_denda_rusak
                $dendaDetail = ($alat->harga_alat * ($persenDendaRusak / 100)) * $jumlah;
                $jumlahRusak += $jumlah;
            } elseif ($kondisi == 'hilang') {
                // Hilang: charge full harga_alat
                $dendaDetail = $alat->harga_alat * $jumlah;
                $jumlahHilang += $jumlah;
            }

            PengembalianDetail::create([
                'pengembalian_id' => $pengembalian->pengembalian_id,
                'kondisi_alat' => $kondisi,
                'jumlah' => $jumlah,
                'harga_alat' => $alat->harga_alat,
                'persen_denda' => $kondisi == 'baik' ? 0 : ($kondisi == 'rusak' ? $persenDendaRusak : 100),
                'denda_barang' => $dendaDetail,
            ]);

            $totalDendaBarang += $dendaDetail;
        }

        // ✅ Total denda = HANYA denda barang (TIDAK ada denda keterlambatan)
        $pengembalian->update([
            'total_denda' => $totalDendaBarang,
            'status_denda' => $totalDendaBarang > 0 ? 'belum_lunas' : 'lunas',
        ]);

        $peminjaman->update(['status' => 'dikembalikan']);

        // ✅ Stock management (sama seperti sebelumnya)
        if ($jumlahBaik > 0) {
            $alat->increment('stok_tersedia', $jumlahBaik);
        }

        $hasRusakOrHilang = collect($kondisiDetails)
            ->whereIn('kondisi', ['rusak', 'hilang'])
            ->isNotEmpty();
            
        if ($hasRusakOrHilang) {
            $alat->update(['kondisi' => 'rusak']);
        }
    });

    LogAktivitas::create([
        'user_id' => Auth::id(),
        'aktivitas' => 'Proses Pengembalian',
        'modul' => 'Pengembalian',
        'timestamp' => now(),
    ]);

    return redirect()->route('pengembalian.index')->with('success', 'Pengembalian berhasil diproses!');
}
    // ✅ Bayar Denda
    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'pengembalian_id' => 'required|exists:pengembalian,pengembalian_id',
        ]);

        $pengembalian = Pengembalian::findOrFail($validated['pengembalian_id']);

        DB::transaction(function () use ($pengembalian) {
            $pengembalian->update([
                'status_denda' => 'lunas',
            ]);
        });

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Pembayaran Denda - Rp ' . number_format($pengembalian->total_denda, 0, ',', '.'),
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Denda berhasil dibayarkan!');
    }


    // ✅ API: Proses pengembalian cepat via QR
public function quickProcess(Request $request)
{
    $validated = $request->validate([
        'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
        'alat_unit_id' => 'nullable|exists:alat_units,id',  // ✅ NULLABLE
        'alat_id' => 'nullable|exists:alat,alat_id',  // ✅ ADD THIS
        'kondisi' => 'required|in:baik,rusak,hilang',
        'persen_denda_custom' => 'nullable|numeric|min:0|max:100',
        'tanggal_kembali' => 'required|date',
        'keterangan' => 'nullable|string',
    ]);

    $peminjaman = Peminjaman::findOrFail($validated['peminjaman_id']);
    
    // ✅ Get alat (from unit atau direct)
    $alat = null;
    $alatUnit = null;
    
    if (!empty($validated['alat_unit_id'])) {
        $alatUnit = AlatUnit::findOrFail($validated['alat_unit_id']);
        $alat = $alatUnit->alat;
    } else {
        $alat = Alat::findOrFail($validated['alat_id']);
    }

    // ✅ Calculate denda
    $persenDendaRusak = $alat->persen_denda_rusak ?? 30;
    $kondisi = $validated['kondisi'];
    $jumlah = $peminjaman->jumlah;
    $harga = $alat->harga_alat;

    $dendaDetail = 0;
    if ($alatUnit) {
        if ($kondisi === 'baik') {
            $alatUnit->update(['status' => 'tersedia']);
            $alat->increment('stok_tersedia', $jumlah);
        } elseif ($kondisi === 'rusak') {
            $alatUnit->update(['status' => 'rusak']);
        } elseif ($kondisi === 'hilang') {
            $alatUnit->update(['status' => 'hilang']);
        }
    }

    DB::transaction(function () use ($validated, $peminjaman, $alatUnit, $alat, $kondisi, $dendaDetail, $jumlah) {
        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $validated['peminjaman_id'],
            'alat_unit_id' => $alatUnit?->id,  // ✅ NULLABLE
            'tanggal_kembali_aktual' => $validated['tanggal_kembali'],
            'total_denda' => $dendaDetail,
            'status_denda' => $dendaDetail > 0 ? 'belum_lunas' : 'lunas',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        PengembalianDetail::create([
            'pengembalian_id' => $pengembalian->pengembalian_id,
            'alat_unit_id' => $alatUnit?->id,  // ✅ NULLABLE
            'kondisi_alat' => $kondisi,
            'jumlah' => $jumlah,
            'harga_alat' => $alat->harga_alat,
            'persen_denda' => $kondisi === 'baik' ? 0 : ($kondisi === 'rusak' ? ($validated['persen_denda_custom'] ?? 30) : 100),
            'denda_barang' => $dendaDetail,
        ]);

        $peminjaman->update(['status' => 'dikembalikan']);

        // ✅ Update unit status ONLY if ada unit
        if ($alatUnit) {
            if ($kondisi === 'baik') {
                $alatUnit->update(['status' => 'tersedia']);
                $alat->increment('stok_tersedia', $jumlah);
            } else {
                $alatUnit->update(['status' => $kondisi]);
            }
        }
    });

    LogAktivitas::create([
        'user_id' => Auth::id(),
        'aktivitas' => 'Pengembalian Cepat - ' . $alat->nama_alat,
        'modul' => 'Pengembalian',
        'timestamp' => now(),
    ]);

    return response()->json([
        'success' => true,
        'denda' => $dendaDetail
    ]);
}


// ✅ API: Get peminjaman + detail harga dari QR scan
public function getFromQr(Request $request)
{
    try {
        $qrRaw = $request->input('qr_data');

        if (!$qrRaw) {
            return response()->json(['success' => false, 'message' => 'QR data kosong'], 400);
        }

        $data = json_decode($qrRaw, true);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Format QR tidak valid'], 400);
        }

        // Cari AlatUnit
        $alatUnit = null;
        if (!empty($data['alat_unit_id'])) {
            $alatUnit = AlatUnit::with('alat')->find($data['alat_unit_id']);
        }

        if (!$alatUnit) {
            return response()->json(['success' => false, 'message' => 'Unit tidak ditemukan'], 404);
        }

        $alat = $alatUnit->alat;


        // code Cari peminjaman aktif lama
        // $peminjaman = Peminjaman::where('alat_id', $alat->alat_id)
        //     ->where('status', 'disetujui')
        //     ->whereDoesntHave('pengembalian')
        //     ->latest()
        //     ->first();

        // if (!$peminjaman) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Tidak ada peminjaman aktif untuk barang ini'
        //     ], 404);
        // }

        // Cari peminjaman aktif - spesifik per unit dulu, fallback ke guest
        // Cari peminjaman aktif HANYA untuk unit yang di-scan
        $peminjaman = Peminjaman::where('alat_unit_id', $alatUnit->id)
            ->where('status', 'disetujui')
            ->whereDoesntHave('pengembalian')
            ->latest()
            ->first();

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Barang ini sedang tidak dipinjam'
            ], 404);
        }

        $namaPeminjam = $peminjaman->nama_peminjam_guest
            ?? optional($peminjaman->user)->name
            ?? 'Guest';

        return response()->json([
            'success' => true,
            'alat' => [
                'peminjaman_id'        => $peminjaman->peminjaman_id,
                'alat_unit_id'         => $alatUnit->id,
                'alat_id'              => $alat->alat_id,
                'nama_alat'            => $alat->nama_alat,
                'unit_number'          => $alatUnit->unit_number,
                'nama_peminjam'        => $namaPeminjam,
                'jumlah'               => $peminjaman->jumlah,
                'harga_alat'           => (float) $alat->harga_alat,
                'persen_default_rusak' => (int) ($alat->persen_denda_rusak ?? 30),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

// ✅ NEW: Show quick return form
public function quickReturnForm()
{
    return view('pages.pengembalian.quick-return');
}



    public function destroy(Pengembalian $pengembalian)
    {
        // ✅ NEW: Kembalikan stok jika pengembalian dihapus
        DB::transaction(function () use ($pengembalian) {
            // Hitung barang yang dikembalikan per kondisi
            $jumlahBaik = $pengembalian->details()->where('kondisi_alat', 'baik')->sum('jumlah');
            
            // Jika ada barang baik yang dikembalikan, kembalikan ke stok (karena sudah dikurangi saat store)
            if ($jumlahBaik > 0) {
                $pengembalian->peminjaman->alat->decrement('stok_tersedia', $jumlahBaik);
            }
            
            // Hapus details
            PengembalianDetail::where('pengembalian_id', $pengembalian->pengembalian_id)->delete();
            
            // Hapus pengembalian
            $pengembalian->delete();
        });

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus!');
    }
}