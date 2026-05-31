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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('tanggal_klaim')->useCurrent();
            $table->text('bukti_teks');
            $table->string('bukti_foto', 255)->nullable();
            $table->enum('status_verif', ['pending', 'approved', 'rejected', 'clarification_required'])->default('pending');
            $table->string('token_pengambilan', 16)->unique()->nullable();
            $table->text('catatan_evaluasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
