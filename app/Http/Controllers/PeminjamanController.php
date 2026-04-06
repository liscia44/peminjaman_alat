<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    // ======= GUEST FORM (Tanpa Login) =======
    public function guestForm()
    {
        $alats = Alat::where('stok_tersedia', '>', 0)->get();
        return view('pages.peminjaman.guest-form', compact('alats'));
    }

    public function guestStore(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam_guest' => 'required|string|max:255',
            'telepon_peminjam_guest' => 'required|string|max:20',
            'alat_id' => 'required|exists:alat,alat_id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_peminjaman',
            'tujuan_peminjaman' => 'nullable|string',
        ]);

        $alat = Alat::find($validated['alat_id']);

        // Validasi stok
        if ($validated['jumlah'] > $alat->stok_tersedia) {
            return back()->withErrors(['jumlah' => 'Stok tidak cukup untuk alat ini. Maksimal: ' . $alat->stok_tersedia . ' unit']);
        }

        // Buat peminjaman dengan kode otomatis
        $peminjaman = Peminjaman::create([
            'user_id' => null,
            'alat_id' => $validated['alat_id'],
            'nama_peminjam_guest' => $validated['nama_peminjam_guest'],
            'telepon_peminjam_guest' => $validated['telepon_peminjam_guest'],
            'jumlah' => $validated['jumlah'],
            'tanggal_peminjaman' => $validated['tanggal_peminjaman'],
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'tujuan_peminjaman' => $validated['tujuan_peminjaman'] ?? null,
            'status' => 'menunggu',
            'disetujui_oleh' => null,
        ]);

        return redirect()->route('peminjaman.guest')
            ->with('success', "Peminjaman berhasil diajukan! Kode peminjaman Anda: <strong>{$peminjaman->kode_peminjaman}</strong>. Admin akan menghubungi Anda di {$validated['telepon_peminjam_guest']}")
            ->with('kode_peminjaman', $peminjaman->kode_peminjaman);
    }

    // ======= AUTHENTICATED ROUTES =======
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'peminjam') {
            $peminjaman = Peminjaman::where('user_id', $user->user_id)
                ->with('alat', 'petugas')
                ->latest()
                ->get();
            return view('pages.peminjaman.index-peminjam', compact('peminjaman'));
        }

        if ($user->role === 'petugas') {
            $peminjaman = Peminjaman::with('alat', 'user', 'petugas')
                ->latest()
                ->paginate(15);
            return view('pages.peminjaman.index-petugas', compact('peminjaman'));
        }

        if ($user->role === 'admin') {
            $peminjaman = Peminjaman::with('alat', 'user', 'petugas')
                ->latest()
                ->paginate(15);
            return view('pages.peminjaman.index-admin', compact('peminjaman'));
        }
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

        $peminjaman->update([
            'status' => $validated['status'],
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

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
        $peminjaman->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

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
        $peminjaman->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus peminjaman',
            'modul' => 'Peminjaman',
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Peminjaman dihapus!');
    }
}