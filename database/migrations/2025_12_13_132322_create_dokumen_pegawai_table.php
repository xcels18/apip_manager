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
        Schema::create('dokumen_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->enum('jenis_dokumen', [
                // Blok 1: Data Pribadi
                'KTP',
                'Kartu Keluarga',
                'Akta Lahir',
                // Blok 2: Data Kepegawaian
                'SK CPNS',
                'SK PPPK',
                'SK Pangkat',
                'SK Jabatan',
                'SK Mutasi',
                // Blok 5: Data Penunjang
                'NPWP',
                'Buku Rekening',
                'Sertifikat Kompetensi',
                'Lainnya'
            ]);
            $table->string('nama_dokumen', 200);
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pegawai');
    }
};
