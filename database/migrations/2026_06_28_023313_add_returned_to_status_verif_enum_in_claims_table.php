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
        Schema::table('claims', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims MODIFY COLUMN status_verif ENUM('pending', 'approved', 'rejected', 'clarification_required', 'returned') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE claims MODIFY COLUMN status_verif ENUM('pending', 'approved', 'rejected', 'clarification_required') DEFAULT 'pending'");
        });
    }
};
