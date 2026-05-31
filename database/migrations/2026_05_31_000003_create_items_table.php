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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->text('deskripsi_rahasia');
            $table->string('foto', 255)->nullable();
            $table->enum('post_type', ['lost', 'found']);
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->dateTime('tanggal_lapor');
            
            // Foreign Keys
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['post_type', 'status', 'visibility']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
