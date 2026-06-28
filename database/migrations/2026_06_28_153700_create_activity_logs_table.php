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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reference_id'); // ID of the claim or item
            $table->string('reference_type'); // 'App\\Models\\Claim' or 'App\\Models\\Item'
            $table->string('action_type'); // 'created', 'status_changed', 'handover', etc.
            $table->text('description'); // Display text
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Actor
            $table->json('metadata')->nullable(); // Additional data like tokens, old/new status
            $table->timestamps();
            
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
