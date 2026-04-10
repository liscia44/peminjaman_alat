<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            if (!Schema::hasColumn('pengembalian', 'alat_unit_id')) {
                $table->unsignedBigInteger('alat_unit_id')->nullable()->after('peminjaman_id');
                $table->foreign('alat_unit_id')
                    ->references('id')
                    ->on('alat_units')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['alat_unit_id']);
            $table->dropColumnIfExists('alat_unit_id');
        });
    }
};