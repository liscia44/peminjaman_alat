<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('mata_pelajaran')->nullable()->after('kelas');
            $table->string('jam_peminjaman')->nullable()->after('mata_pelajaran');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['mata_pelajaran', 'jam_peminjaman']);
        });
    }
};