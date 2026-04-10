<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'alat_unit_id')) {
                $table->unsignedBigInteger('alat_unit_id')->nullable()->after('alat_id');
                $table->foreign('alat_unit_id')
                    ->references('id')
                    ->on('alat_units')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['alat_unit_id']);
            $table->dropColumnIfExists('alat_unit_id');
        });
    }
};