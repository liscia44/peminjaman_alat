<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'alat_id',
        'jumlah',
        'kelas',
        'mata_pelajaran',  // ← TAMBAH INI
        'jam_peminjaman',
        'tanggal_peminjaman',
        'tanggal_kembali_rencana',
        'tujuan_peminjaman',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'nama_peminjam_guest',
        'telepon_peminjam_guest',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'menunggu',
    ];

    // ✅ Auto generate kode peminjaman saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->kode_peminjaman) {
                $model->kode_peminjaman = self::generateKodePeminjaman();
            }
        });
    }

    // ✅ Generate kode unik (format: PMJ-YYYYMMDD-XXXXX)
    public static function generateKodePeminjaman()
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(5));
        $kode = "PMJ-{$date}-{$random}";

        // Jika sudah ada, generate ulang
        while (self::where('kode_peminjaman', $kode)->exists()) {
            $random = strtoupper(Str::random(5));
            $kode = "PMJ-{$date}-{$random}";
        }

        return $kode;
    }

    // ✅ Helper: Dapatkan nama peminjam (guest atau user)
    public function getNamaPeminjam()
    {
        if ($this->user_id) {
            return $this->user->username ?? $this->user->nama;
        }
        return $this->nama_peminjam_guest ?? '-';
    }

    // ✅ Helper: Dapatkan telepon peminjam (guest atau user)
    public function getTeleponPeminjam()
    {
        if ($this->user_id) {
            return $this->user->telepon ?? '-';
        }
        return $this->telepon_peminjam_guest ?? '-';
    }

    // ✅ Helper: Cek apakah peminjaman dari guest atau user
    public function isGuest()
    {
        return $this->user_id === null;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id', 'alat_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh', 'user_id');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id', 'peminjaman_id');
    }

    public function getRouteKeyName()
    {
        return 'peminjaman_id';
    }
}