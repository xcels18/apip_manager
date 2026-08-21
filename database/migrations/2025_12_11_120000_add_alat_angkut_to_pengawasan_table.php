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
            $table->enum('alat_angkut', ['darat', 'udara'])->default('darat')->after('lokasi_penugasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropColumn('alat_angkut');
        });
    }
};

