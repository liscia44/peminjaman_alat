<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // ======= GUEST FORM (Tanpa Login) =======
     // ✅ GUEST FORM - Tidak perlu pass data
    public function guestForm()
    {
        return view('pages.peminjaman.guest-form');
    }

   public function guestStore(Request $request)
{
    $validated = $request->validate([
        'nama_peminjam_guest' => 'required|string|max:255',
        'alat_id' => 'required|exists:alat,alat_id',
        'jumlah' => 'required|integer|min:1',
        'kelas' => 'required|string|max:50',
        'mata_pelajaran' => 'required|string|max:100',  
        'jam_peminjaman' => 'required|string|max:50',
        'jam_kembali' => 'required|string|max:50',
        'tujuan_peminjaman' => 'nullable|string',
        'tanggal_peminjaman' => 'required|date',
    ]);

    $alat = Alat::find($validated['alat_id']);
    
    if (!$alat) {
        return back()->withErrors(['alat_id' => 'Alat tidak ditemukan']);
    }

    if ($validated['jumlah'] > $alat->stok_tersedia) {
        return back()->withErrors(['jumlah' => "Stok tidak cukup. Tersedia hanya {$alat->stok_tersedia} unit"]);
    }

    $peminjaman = null;

    try {
        DB::transaction(function () use ($validated, $alat, &$peminjaman) {
            
            // ✅ FIX #1: Cek apakah alat sudah ada peminjaman AKTIF (SEBELUM cari unit)
            $existingBorrow = Peminjaman::where('alat_id', $validated['alat_id'])
                ->where('status', 'disetujui')
                ->whereDoesntHave('pengembalian')
                ->lockForUpdate()
                ->first();

            if ($existingBorrow) {
                throw new \Exception('❌ Alat ini sudah sedang dipinjam oleh ' . ($existingBorrow->nama_peminjam_guest ?? 'peminjam lain') . '. Tunggu hingga dikembalikan.');
            }

            // ✅ FIX #2: Cari unit TERSEDIA (lock dari awal)
            $unitTersedia = \App\Models\AlatUnit::where('alat_id', $validated['alat_id'])
                ->whereNotIn('status', ['dipinjam', 'rusak', 'hilang'])
                ->lockForUpdate()
                ->first();

            if (!$unitTersedia) {
                throw new \Exception('❌ Tidak ada unit yang tersedia untuk alat ini.');
            }

            // ✅ FIX #3: DOUBLE CHECK unit ini tidak sedang dipinjam
            $unitHasActiveBorrow = Peminjaman::where('alat_unit_id', $unitTersedia->id)
                ->where('status', 'disetujui')
                ->whereDoesntHave('pengembalian')
                ->lockForUpdate()
                ->exists();

            if ($unitHasActiveBorrow) {
                throw new \Exception('❌ Unit #' . $unitTersedia->unit_number . ' sedang dipinjam. Coba unit lain.');
            }

            // ✅ FIX #4: Cek stok LAGI (sebelum create, bisa berubah)
            $alat->refresh();
            if ($validated['jumlah'] > $alat->stok_tersedia) {
                throw new \Exception('❌ Stok tidak cukup. Tersedia hanya ' . $alat->stok_tersedia . ' unit.');
            }

            // ✅ SEKARANG BARU CREATE peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => null,
                'alat_id' => $validated['alat_id'],
                'alat_unit_id' => $unitTersedia->id,
                'nama_peminjam_guest' => $validated['nama_peminjam_guest'],
                'jumlah' => $validated['jumlah'],
                'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
                'tanggal_kembali_rencana' => $validated['tanggal_peminjaman'],
                'tujuan_peminjaman' => $validated['tujuan_peminjaman'] ?? null,
                'kelas' => $validated['kelas'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
                'jam_peminjaman' => $validated['jam_peminjaman'],
                'jam_kembali' => $validated['jam_kembali'],
                'status' => 'disetujui',
                'disetujui_oleh' => 1,
                'tanggal_disetujui' => now(),
            ]);

            // ✅ UPDATE status unit
            $unitTersedia->update(['status' => 'dipinjam']);

            // ✅ Kurangi stok
            $alat->decrement('stok_tersedia', $validated['jumlah']);

            // ✅ Log
            LogAktivitas::create([
                'user_id' => 1,
                'aktivitas' => "✅ Peminjaman Guest - {$alat->nama_alat} (Unit #{$unitTersedia->unit_number})",
                'modul' => 'Peminjaman',
                'timestamp' => now(),
            ]);

        }, 3);

    } catch (\Exception $e) {
        \Log::error('Peminjaman Guest Error', [
            'message' => $e->getMessage(),
            'alat_id' => $validated['alat_id'],
            'nama_peminjam' => $validated['nama_peminjam_guest'],
            'trace' => $e->getTraceAsString(),
        ]);
        
        return back()
            ->withErrors(['alat_id' => $e->getMessage()])
            ->withInput();
    }

    if (!$peminjaman) {
        return back()->withErrors(['alat_id' => 'Gagal membuat peminjaman. Silakan coba lagi.']);
    }

    return redirect()->route('peminjaman.guest')
        ->with('success', "✅ PEMINJAMAN DITERIMA! 🎉<br>Kode: <strong>{$peminjaman->kode_peminjaman}</strong><br>Unit: <strong>#{$peminjaman->alatUnit->unit_number}</strong>")
        ->with('kode_peminjaman', $peminjaman->kode_peminjaman);
}


    // ======= AUTHENTICATED ROUTES =======
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'peminjam') {
            $peminjaman = Peminjaman::where('user_id', $user->user_id)
                ->with('alat', 'petugas', 'user')
                ->latest()
                ->get();
            return view('pages.peminjaman.index-peminjam', compact('peminjaman'));
        }

        // Admin & Petugas lihat semua peminjaman (termasuk guest)
        $allPeminjaman = Peminjaman::with('alat', 'user', 'petugas')
            ->latest()
            ->get();

        return view('pages.peminjaman.index-petugas', compact('allPeminjaman'));
    }

    public function create()
    {
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        return view('pages.peminjaman.create', compact('alats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alat_id' => 'required|exists:alat,alat_id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_peminjaman',
            'tujuan_peminjaman' => 'nullable|string',
            'kelas' => 'required|string|max:50',
            'mata_pelajaran' => 'required|string|max:100',  
            'jam_peminjaman' => 'required|string|max:50',
        ]);

        $alat = Alat::find($validated['alat_id']);

        if ($validated['jumlah'] > $alat->stok_tersedia) {
            return back()->withErrors(['jumlah' => 'Stok tidak cukup']);
        }

        Peminjaman::create([
            'user_id' => auth()->id(),
            'alat_id' => $validated['alat_id'],
            'jumlah' => $validated['jumlah'],
            'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'tujuan_peminjaman' => $validated['tujuan_peminjaman'] ?? null,
            'kelas' => $validated['kelas'],
            'mata_pelajaran' => $validated['mata_pelajaran'],  
            'jam_peminjaman' => $validated['jam_peminjaman'],
            'status' => 'menunggu',
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Membuat peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Peminjaman berhasil diajukan!');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        // ✅ FIXED: Handle stock change saat approve/reject
        if ($validated['status'] == 'disetujui' && $peminjaman->status == 'menunggu') {
            // Approve: kurangi stok
            DB::transaction(function () use ($peminjaman, $validated) {
                $peminjaman->alat->decrement('stok_tersedia', $peminjaman->jumlah);
                
                $peminjaman->update([
                    'status' => $validated['status'],
                    'disetujui_oleh' => auth()->id(),
                    'tanggal_disetujui' => now(),
                ]);
            });
        } else if ($validated['status'] == 'ditolak' && $peminjaman->status == 'menunggu') {
            // Reject: jangan ubah stok
            $peminjaman->update([
                'status' => $validated['status'],
                'disetujui_oleh' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);
        }

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Mengupdate status peminjaman menjadi ' . $validated['status'],
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Status peminjaman diperbarui!');
    }

    public function approve(Request $request, Peminjaman $peminjaman)
    {
        // ✅ FIXED: Handle stock change saat approve
        DB::transaction(function () use ($peminjaman) {
            // Kurangi stok jika belum disetujui
            if ($peminjaman->status == 'menunggu') {
                $peminjaman->alat->decrement('stok_tersedia', $peminjaman->jumlah);
            }
            
            $peminjaman->update([
                'status' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'tanggal_disetujui' => now(),
            ]);
        });

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menyetujui peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Peminjaman disetujui!');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        // ✅ FIXED: Kembalikan stok jika peminjaman dihapus
        DB::transaction(function () use ($peminjaman) {
            if ($peminjaman->status == 'menunggu') {
                // Jika masih menunggu, tidak perlu kembalikan stok (belum dikurangi)
            } else if ($peminjaman->status == 'disetujui' && !$peminjaman->pengembalian) {
                // Jika sudah disetujui tapi belum dikembalikan, kembalikan stok
                $peminjaman->alat->increment('stok_tersedia', $peminjaman->jumlah);
            }
            
            $peminjaman->delete();
        });

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Peminjaman dihapus!');
    }
}