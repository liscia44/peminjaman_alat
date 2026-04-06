<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Buat user_id nullable untuk guest
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Tambah kolom untuk data guest
            $table->string('kode_peminjaman')->unique()->nullable()->after('peminjaman_id');
            $table->string('nama_peminjam_guest')->nullable()->after('user_id');
            $table->string('telepon_peminjam_guest')->nullable()->after('nama_peminjam_guest');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['kode_peminjaman', 'nama_peminjam_guest', 'telepon_peminjam_guest']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};