<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlatController extends Controller
{
    public function index()
    {
        $alats = Alat::with('kategori')->get();
        $userLevel = Auth::user()->level; // ✅ ADDED: Get user level
        return view('pages.alat.index', compact('alats', 'userLevel')); // ✅ UPDATED: Pass userLevel
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('pages.alat.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'nama_alat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode_alat' => 'required|string|unique:alat,kode_alat',
            'stok_total' => 'required|integer|min:1',
            'kondisi' => 'required|in:baik,rusak,hilang',
            'lokasi' => 'nullable|string',
        ]);

        $validated['stok_tersedia'] = $validated['stok_total'];

        $alat = Alat::create($validated);

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Tambah Alat',
            'modul' => 'Alat',
            'timestamp' => now(),
        ]);

        return redirect()->route('alat.index')->with('success', 'Alat berhasil ditambahkan!');
    }

    public function edit(Alat $alat)
    {
        $kategoris = Kategori::all();
        return view('pages.alat.edit', compact('alat', 'kategoris'));
    }

    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'nama_alat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kode_alat' => 'required|string|unique:alat,kode_alat,' . $alat->alat_id . ',alat_id',
            'stok_total' => 'required|integer|min:1',
            'kondisi' => 'required|in:baik,rusak,hilang',
            'lokasi' => 'nullable|string',
        ]);

        $alat->update($validated);

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Update Alat',
            'modul' => 'Alat',
            'timestamp' => now(),
        ]);

        return redirect()->route('alat.index')->with('success', 'Alat berhasil diupdate!');
    }

    public function destroy(Alat $alat)
    {
        $alat->delete();

        // Log aktivitas
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Alat',
            'modul' => 'Alat',
            'timestamp' => now(),
        ]);

        return redirect()->route('alat.index')->with('success', 'Alat berhasil dihapus!');
    }
}