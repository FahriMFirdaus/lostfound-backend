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
        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->unique()->constrained('claims')->onDelete('restrict');
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->string('foto_materai', 255)->nullable();
            $table->string('foto_serah_terima', 255);
            $table->dateTime('tanggal_serah_terima')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handovers');
    }
};
