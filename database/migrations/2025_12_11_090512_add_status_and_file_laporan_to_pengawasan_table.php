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
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->enum('status', ['belum_selesai', 'selesai'])->default('belum_selesai')->after('jenis_penugasan');
            $table->string('file_laporan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropColumn(['status', 'file_laporan']);
        });
    }
};
