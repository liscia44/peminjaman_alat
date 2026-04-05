<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reset total schema public — drop semua tabel, type, function sekaligus.
     * File ini HARUS jalan paling pertama (timestamp 0000_00_00_000000).
     */
    public function up(): void
    {
        // Drop dan recreate schema public = bersih total tanpa perlu superuser
        DB::statement('DROP SCHEMA public CASCADE');
        DB::statement('CREATE SCHEMA public');
        DB::statement('GRANT ALL ON SCHEMA public TO public');
    }

    public function down(): void
    {
        // tidak bisa di-rollback
    }
};
