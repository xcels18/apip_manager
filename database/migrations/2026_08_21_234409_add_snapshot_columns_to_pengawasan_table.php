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
            $table->string('penandatangan_definitif_nama', 200)->nullable()->after('penandatangan_plh_jabatan');
            $table->string('penandatangan_definitif_nip', 100)->nullable()->after('penandatangan_definitif_nama');
            $table->string('penandatangan_definitif_jabatan', 200)->nullable()->after('penandatangan_definitif_nip');
            
            $table->string('kop_pemerintah', 255)->nullable()->after('penandatangan_definitif_jabatan');
            $table->string('kop_instansi', 255)->nullable()->after('kop_pemerintah');
            $table->string('kop_jalan', 255)->nullable()->after('kop_instansi');
            $table->string('kop_email', 255)->nullable()->after('kop_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropColumn([
                'penandatangan_definitif_nama',
                'penandatangan_definitif_nip',
                'penandatangan_definitif_jabatan',
                'kop_pemerintah',
                'kop_instansi',
                'kop_jalan',
                'kop_email',
            ]);
        });
    }
};
