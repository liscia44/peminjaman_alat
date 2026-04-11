<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlatUnit extends Model
{
    protected $table = 'alat_units';
    protected $guarded = [];

    protected $fillable = [
        'alat_id',
        'unit_number',
        'status',  // ✅ ADD THIS
        'qr_code',
    ];

    protected $attributes = [
        'status' => 'baik',  // ✅ ADD THIS
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id', 'alat_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'alat_unit_id');
    }

    // ✅ ADD THIS
    public function pengembalian()
    {
        return $this->hasMany(PengembalianDetail::class, 'alat_unit_id');
    }

    // Check apakah unit sedang dipinjam
    public function isPeminjam()
    {
        return $this->status === 'dipinjam';
    }

    // Get peminjam saat ini
    public function getPeminjamSekarang()
    {
        return $this->peminjaman()
            ->where('status', 'disetujui')
            ->latest()
            ->first();
    }
}