<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('books', function (Blueprint $table) {
            $table->string('cover_image_url')->nullable()->after('cover_image');
        });
    }
    public function down() {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('cover_image_url');
        });
    }
};
