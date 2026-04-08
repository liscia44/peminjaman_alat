<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';
    protected $primaryKey = 'alat_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'nomor_unit',
        'deskripsi',
        'kode_alat',
        'stok_total',
        'stok_tersedia',
        'kondisi',
        'qr_code',  
        'lokasi',
        'harga_alat',              // ✅ NEW
        'persen_denda_rusak',      // ✅ NEW
    ];

    // ✅ NEW: Casting untuk tipe data yang tepat
    protected $casts = [
        'harga_alat' => 'decimal:2',
        'persen_denda_rusak' => 'integer',
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'alat_id', 'alat_id');
    }

    public function getRouteKeyName()
    {
        return 'alat_id';
    }

    // ✅ NEW: Helper method untuk hitung denda
    public function hitungDendaBarang($kondisi, $jumlah = 1)
    {
        if ($kondisi == 'baik') {
            return 0;
        } elseif ($kondisi == 'rusak') {
            return ($this->harga_alat * ($this->persen_denda_rusak / 100)) * $jumlah;
        } elseif ($kondisi == 'hilang') {
            return ($this->harga_alat * 1) * $jumlah;  // 100%
        }
        return 0;
    }
}