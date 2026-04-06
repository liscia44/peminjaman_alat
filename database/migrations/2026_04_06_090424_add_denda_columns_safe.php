<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu sebelum tambah kolom
        if (!Schema::hasColumn('pengembalian', 'denda_keterlambatan')) {
            DB::statement('ALTER TABLE pengembalian ADD COLUMN denda_keterlambatan NUMERIC(14,2) DEFAULT 0');
        }

        if (!Schema::hasColumn('pengembalian', 'denda_barang')) {
            DB::statement('ALTER TABLE pengembalian ADD COLUMN denda_barang NUMERIC(14,2) DEFAULT 0');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pengembalian DROP COLUMN IF EXISTS denda_keterlambatan');
        DB::statement('ALTER TABLE pengembalian DROP COLUMN IF EXISTS denda_barang');
    }
};