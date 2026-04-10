<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat_units', function (Blueprint $table) {
            if (!Schema::hasColumn('alat_units', 'status')) {
                $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang'])
                    ->default('tersedia')
                    ->after('unit_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alat_units', function (Blueprint $table) {
            $table->dropColumnIfExists('status');
        });
    }
};