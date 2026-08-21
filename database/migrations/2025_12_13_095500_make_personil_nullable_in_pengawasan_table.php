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
        // For SQLite, we need to recreate the table
        // First, check if we're using SQLite
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support modifying columns with foreign keys
            // We need to recreate the table
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->unsignedBigInteger('penanggung_jawab_id')->nullable()->change();
                $table->unsignedBigInteger('pengendali_teknis_id')->nullable()->change();
                $table->unsignedBigInteger('ketua_tim_id')->nullable()->change();
            });
        } else {
            // For other databases (MySQL, PostgreSQL, etc.)
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->dropForeign(['penanggung_jawab_id']);
                $table->dropForeign(['pengendali_teknis_id']);
                $table->dropForeign(['ketua_tim_id']);
            });

            Schema::table('pengawasan', function (Blueprint $table) {
                $table->foreignId('penanggung_jawab_id')->nullable()->change()->constrained('pegawai')->onDelete('cascade');
                $table->foreignId('pengendali_teknis_id')->nullable()->change()->constrained('pegawai')->onDelete('cascade');
                $table->foreignId('ketua_tim_id')->nullable()->change()->constrained('pegawai')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->unsignedBigInteger('penanggung_jawab_id')->nullable(false)->change();
                $table->unsignedBigInteger('pengendali_teknis_id')->nullable(false)->change();
                $table->unsignedBigInteger('ketua_tim_id')->nullable(false)->change();
            });
        } else {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->dropForeign(['penanggung_jawab_id']);
                $table->dropForeign(['pengendali_teknis_id']);
                $table->dropForeign(['ketua_tim_id']);
            });

            Schema::table('pengawasan', function (Blueprint $table) {
                $table->foreignId('penanggung_jawab_id')->nullable(false)->change()->constrained('pegawai')->onDelete('cascade');
                $table->foreignId('pengendali_teknis_id')->nullable(false)->change()->constrained('pegawai')->onDelete('cascade');
                $table->foreignId('ketua_tim_id')->nullable(false)->change()->constrained('pegawai')->onDelete('cascade');
            });
        }
    }
};

