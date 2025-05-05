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
        // Duplicate migration: the 'loans' table is already created by an earlier migration.
        // Schema::create('loans', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained();
        //     $table->foreignId('book_id')->constrained();
        //     $table->timestamp('borrowed_at')->useCurrent();
        //     $table->timestamp('due_at')->nullable();
        //     $table->timestamp('returned_at')->nullable();
        //     $table->enum('status', ['active', 'returned'])->default('active');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if it exists and wasn't already dropped by another migration
        if (Schema::hasTable('loans')) {
            Schema::dropIfExists('loans');
        }
    }
};