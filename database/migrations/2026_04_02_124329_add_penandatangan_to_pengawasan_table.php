<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->enum('penandatangan_type', ['definitif', 'plh'])->default('definitif')->after('alat_angkut');
            $table->string('penandatangan_plh_nama', 200)->nullable()->after('penandatangan_type');
            $table->string('penandatangan_plh_jabatan', 200)->nullable()->after('penandatangan_plh_nama');
        });
    }

    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropColumn(['penandatangan_type', 'penandatangan_plh_nama', 'penandatangan_plh_jabatan']);
        });
    }
};
