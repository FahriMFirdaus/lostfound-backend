<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims DROP CONSTRAINT IF EXISTS claims_status_verif_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims ADD CONSTRAINT claims_status_verif_check CHECK (status_verif::text = ANY (ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying, 'clarification_required'::character varying, 'returned'::character varying]::text[]))");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims MODIFY COLUMN status_verif ENUM('pending', 'approved', 'rejected', 'clarification_required', 'returned') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims DROP CONSTRAINT IF EXISTS claims_status_verif_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims ADD CONSTRAINT claims_status_verif_check CHECK (status_verif::text = ANY (ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying, 'clarification_required'::character varying]::text[]))");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims MODIFY COLUMN status_verif ENUM('pending', 'approved', 'rejected', 'clarification_required') DEFAULT 'pending'");
        }
    }
};
