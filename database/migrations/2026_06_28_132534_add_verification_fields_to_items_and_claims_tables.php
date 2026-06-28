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
        Schema::table('items', function (Blueprint $table) {
            $table->text('bukti_teks')->nullable()->after('foto');
            $table->string('foto_ktp', 255)->nullable()->after('bukti_teks');
            $table->string('foto_bukti_pendukung', 255)->nullable()->after('foto_ktp');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->string('foto_ktp', 255)->nullable()->after('bukti_teks');
            $table->renameColumn('bukti_foto', 'foto_bukti_pendukung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('foto_ktp');
            $table->renameColumn('foto_bukti_pendukung', 'bukti_foto');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['bukti_teks', 'foto_ktp', 'foto_bukti_pendukung']);
        });
    }
};
