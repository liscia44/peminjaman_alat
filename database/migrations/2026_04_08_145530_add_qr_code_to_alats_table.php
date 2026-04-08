<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->string('nomor_unit')->nullable()->after('nama_alat'); // Misal: "Proyektor 1", "Laptop 2"
            $table->text('qr_code')->nullable()->after('nomor_unit'); // Store QR as base64
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn(['nomor_unit', 'qr_code']);
        });
    }
};