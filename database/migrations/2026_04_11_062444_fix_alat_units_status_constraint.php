<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop constraint lama
        DB::statement("ALTER TABLE alat_units DROP CONSTRAINT IF EXISTS alat_units_status_check");
        
        // Tambah constraint baru dengan nilai yang benar
        DB::statement("ALTER TABLE alat_units ADD CONSTRAINT alat_units_status_check CHECK (status::text = ANY (ARRAY['baik', 'rusak', 'hilang']::text[]))");
        
        // Update data lama yang pakai nilai lama
        DB::statement("UPDATE alat_units SET status = 'baik' WHERE status NOT IN ('baik', 'rusak', 'hilang')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE alat_units DROP CONSTRAINT IF EXISTS alat_units_status_check");
        DB::statement("ALTER TABLE alat_units ADD CONSTRAINT alat_units_status_check CHECK (status::text = ANY (ARRAY['baik', 'rusak', 'maintenance']::text[]))");
    }
};