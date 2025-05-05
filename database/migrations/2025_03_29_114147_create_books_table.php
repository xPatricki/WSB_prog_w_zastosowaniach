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
        // Duplicate migration: the 'books' table is already created by an earlier migration.
        // Schema::create('books', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('author');
        //     $table->text('description')->nullable();
        //     $table->string('cover_image')->nullable();
        //     $table->enum('status', ['available', 'borrowed'])->default('available');
        //     $table->boolean('featured')->default(false);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if it exists and wasn't already dropped by another migration
        if (Schema::hasTable('books')) {
            Schema::dropIfExists('books');
        }
    }
};