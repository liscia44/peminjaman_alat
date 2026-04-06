<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
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
        // ✅ FIXED: Allow 0 values, filter them later
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

        // ✅ Filter out items with jumlah = 0
        $kondisiDetails = array_filter($validated['kondisi_details'], function($item) {
            return $item['jumlah'] > 0;
        });

        // ✅ Reindex array after filtering
        $kondisiDetails = array_values($kondisiDetails);

        if (empty($kondisiDetails)) {
            return redirect()->back()
                ->withErrors(['kondisi_details' => "Minimal ada 1 barang yang dikembalikan"])
                ->withInput();
        }

        $totalJumlahDikembalikan = 0;
        foreach ($kondisiDetails as $detail) {
            $totalJumlahDikembalikan += $detail['jumlah'];
        }

        if ($totalJumlahDikembalikan != $peminjaman->jumlah) {
            return redirect()->back()
                ->withErrors(['kondisi_details' => "Total jumlah barang yang dikembalikan harus {$peminjaman->jumlah}. Anda memasukkan {$totalJumlahDikembalikan}"])
                ->withInput();
        }

        $tanggalKembali = Carbon::parse($request->tanggal_kembali_aktual);
        $jatuhTempo = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $keterlambatan = max(0, $tanggalKembali->diffInDays($jatuhTempo, false) * -1);

        $tarifDendaHarian = 50000;
        $dendaKeterlambatan = $keterlambatan * $tarifDendaHarian;

        $persenDendaRusak = $alat->persen_denda_rusak ?? 30;

        DB::transaction(function () use (
            $validated,
            $kondisiDetails,
            $peminjaman,
            $alat,
            $keterlambatan,
            $tarifDendaHarian,
            $dendaKeterlambatan,
            $persenDendaRusak,
            $request
        ) {
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
                'keterlambatan_hari' => $keterlambatan,
                'tarif_denda_per_hari' => $tarifDendaHarian,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'total_denda' => 0,
                'status_denda' => 'belum_lunas',
                'keterangan' => $validated['keterangan'],
            ]);

            $totalDendaBarang = 0;
            $jumlahBaik = 0;
            $jumlahRusak = 0;
            $jumlahHilang = 0;

            foreach ($kondisiDetails as $detail) {
                $kondisi = $detail['kondisi'];
                $jumlah = $detail['jumlah'];
                $dendaDetail = 0;

                if ($kondisi == 'baik') {
                    $dendaDetail = 0;
                    $jumlahBaik += $jumlah;
                } elseif ($kondisi == 'rusak') {
                    $dendaDetail = ($alat->harga_alat * ($persenDendaRusak / 100)) * $jumlah;
                    $jumlahRusak += $jumlah;
                } elseif ($kondisi == 'hilang') {
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

            $totalDenda = $dendaKeterlambatan + $totalDendaBarang;
            $pengembalian->update([
                'total_denda' => $totalDenda,
                'status_denda' => $totalDenda > 0 ? 'belum_lunas' : 'lunas',
            ]);

            $peminjaman->update(['status' => 'dikembalikan']);

            // ✅ FIXED: Adjust stok berdasarkan kondisi barang yang dikembalikan
            // Saat peminjaman disetujui, stok_tersedia dikurangi sebanyak jumlah peminjaman
            // Saat pengembalian:
            // - Barang BAIK: stok_tersedia bertambah (bisa dipinjam lagi)
            // - Barang RUSAK: stok_tersedia tetap (tidak bisa dipinjam, dicatat sebagai rusak)
            // - Barang HILANG: stok_tersedia tetap (tidak bisa dipinjam, dicatat sebagai hilang)
            
            // Total yang bisa dipinjam lagi hanya barang BAIK
            if ($jumlahBaik > 0) {
                $alat->increment('stok_tersedia', $jumlahBaik);
            }

            // ✅ Update kondisi alat jika ada rusak atau hilang
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