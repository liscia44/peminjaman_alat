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
        
        // Summary untuk dashboard
        $totalDenda = $pengembalian->sum('total_denda');
        $dendaBelumLunas = $pengembalian->where('status_denda', 'belum_lunas')->sum('total_denda');
        
        // Hitung alat rusak dan hilang dari detail
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
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_details' => 'required|array',
            'kondisi_details.*.kondisi' => 'required|in:baik,rusak,hilang',
            'kondisi_details.*.jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
        $alat = $peminjaman->alat;

        // ═════════════════════════════════════════════════════════════
        // VALIDASI TOTAL JUMLAH BARANG
        // ═════════════════════════════════════════════════════════════
        $totalJumlahDikembalikan = 0;
        foreach ($validated['kondisi_details'] as $detail) {
            $totalJumlahDikembalikan += $detail['jumlah'];
        }

        if ($totalJumlahDikembalikan != $peminjaman->jumlah) {
            return redirect()->back()
                ->withErrors(['kondisi_details' => "Total jumlah barang yang dikembalikan harus {$peminjaman->jumlah}. Anda memasukkan {$totalJumlahDikembalikan}"])
                ->withInput();
        }

        // ═════════════════════════════════════════════════════════════
        // HITUNG KETERLAMBATAN
        // ═════════════════════════════════════════════════════════════
        $tanggalKembali = Carbon::parse($request->tanggal_kembali_aktual);
        $jatuhTempo = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $keterlambatan = max(0, $tanggalKembali->diffInDays($jatuhTempo, false) * -1);

        // ═════════════════════════════════════════════════════════════
        // HITUNG DENDA KETERLAMBATAN (Fixed: Rp 50.000/hari)
        // ═════════════════════════════════════════════════════════════
        $tarifDendaHarian = 50000;
        $dendaKeterlambatan = $keterlambatan * $tarifDendaHarian;

        // ═════════════════════════════════════════════════════════════
        // HITUNG DENDA BARANG PER KONDISI
        // ═════════════════════════════════════════════════════════════
        $persenDendaRusak = $alat->persen_denda_rusak ?? 30;

        DB::transaction(function () use (
            $validated,
            $peminjaman,
            $alat,
            $keterlambatan,
            $tarifDendaHarian,
            $dendaKeterlambatan,
            $persenDendaRusak,
            $request
        ) {
            // Buat record pengembalian utama
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
                'keterlambatan_hari' => $keterlambatan,
                'tarif_denda_per_hari' => $tarifDendaHarian,
                'denda_keterlambatan' => $dendaKeterlambatan,
                'total_denda' => 0, // Akan diupdate setelah hitung detail
                'status_denda' => 'belum_lunas',
                'keterangan' => $validated['keterangan'],
            ]);

            // Hitung denda barang dan buat detail records
            $totalDendaBarang = 0;
            $jumlahBaik = 0;

            foreach ($validated['kondisi_details'] as $detail) {
                $kondisi = $detail['kondisi'];
                $jumlah = $detail['jumlah'];
                $dendaDetail = 0;

                if ($kondisi == 'baik') {
                    $dendaDetail = 0;
                    $jumlahBaik += $jumlah;
                } elseif ($kondisi == 'rusak') {
                    $dendaDetail = ($alat->harga_alat * ($persenDendaRusak / 100)) * $jumlah;
                } elseif ($kondisi == 'hilang') {
                    $dendaDetail = $alat->harga_alat * $jumlah;
                }

                // Buat detail record
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

            // Update total denda pada pengembalian
            $totalDenda = $dendaKeterlambatan + $totalDendaBarang;
            $pengembalian->update([
                'total_denda' => $totalDenda,
                'status_denda' => $totalDenda > 0 ? 'belum_lunas' : 'lunas',
            ]);

            // Update status peminjaman
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan stok untuk barang yang baik/rusak
            if ($jumlahBaik > 0) {
                $alat->increment('stok_tersedia', $jumlahBaik);
            }

            // Update kondisi alat jika ada rusak atau hilang
            $hasRusakOrHilang = collect($validated['kondisi_details'])
                ->whereIn('kondisi', ['rusak', 'hilang'])
                ->isNotEmpty();
                
            if ($hasRusakOrHilang) {
                $alat->update(['kondisi' => 'rusak']);
            }
        });

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Proses Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Pengembalian berhasil diproses!');
    }

    public function destroy(Pengembalian $pengembalian)
    {
        // Hapus semua detail terlebih dahulu
        PengembalianDetail::where('pengembalian_id', $pengembalian->pengembalian_id)->delete();
        
        $pengembalian->delete();

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Pengembalian',
            'modul' => 'Pengembalian',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus!');
    }
}