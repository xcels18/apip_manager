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
        Schema::table('pegawai', function (Blueprint $table) {
            // BLOK 1: DATA PRIBADI
            $table->string('gelar_depan', 50)->nullable()->after('nama');
            $table->string('gelar_belakang', 100)->nullable()->after('gelar_depan');
            $table->string('tempat_lahir', 100)->nullable()->after('gelar_belakang');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('tanggal_lahir');
            $table->string('agama', 50)->nullable()->after('jenis_kelamin');
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])->nullable()->after('agama');
            $table->text('alamat_lengkap')->nullable()->after('status_perkawinan');
            $table->string('no_hp', 20)->nullable()->after('alamat_lengkap');
            $table->string('foto_pegawai')->nullable()->after('email');

            // BLOK 2: DATA KEPEGAWAIAN
            $table->enum('status_pegawai', ['PNS', 'PPPK', 'Honorer'])->default('PNS')->after('foto_pegawai');
            $table->string('pangkat', 100)->nullable()->after('status_pegawai');
            $table->date('tmt_pangkat')->nullable()->after('golongan');
            $table->date('tmt_jabatan')->nullable()->after('jabatan');
            $table->string('unit_kerja', 200)->nullable()->after('tmt_jabatan');
            $table->string('nomor_sk_pengangkatan')->nullable()->after('unit_kerja');
            $table->string('pejabat_sk')->nullable()->after('nomor_sk_pengangkatan');

            // BLOK 5: DATA PENUNJANG
            $table->string('npwp', 30)->nullable()->after('pejabat_sk');
            $table->string('nomor_bpjs', 30)->nullable()->after('npwp');
            $table->string('nomor_rekening', 50)->nullable()->after('nomor_bpjs');
            $table->string('bank', 100)->nullable()->after('nomor_rekening');
            $table->enum('status_sertifikasi_apip', ['Belum Tersertifikasi', 'Tersertifikasi', 'Dalam Proses'])->default('Belum Tersertifikasi')->after('bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'gelar_depan',
                'gelar_belakang',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'agama',
                'status_perkawinan',
                'alamat_lengkap',
                'no_hp',
                'foto_pegawai',
                'status_pegawai',
                'pangkat',
                'tmt_pangkat',
                'tmt_jabatan',
                'unit_kerja',
                'nomor_sk_pengangkatan',
                'pejabat_sk',
                'npwp',
                'nomor_bpjs',
                'nomor_rekening',
                'bank',
                'status_sertifikasi_apip',
            ]);
        });
    }
};
