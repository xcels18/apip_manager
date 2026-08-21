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
        Schema::create('pengawasan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_st', 100)->unique(); // Nomor Surat Tugas
            $table->date('tanggal_st'); // Tanggal Surat Tugas
            $table->integer('lama_penugasan'); // Lama Penugasan (hari)
            $table->text('uraian_penugasan'); // Uraian Penugasan
            $table->string('lokasi_penugasan', 200); // Lokasi Penugasan
            $table->string('jenis_penugasan', 100); // Jenis Penugasan
            $table->foreignId('penanggung_jawab_id')->constrained('pegawai')->onDelete('cascade'); // Penanggung Jawab
            $table->foreignId('pengendali_teknis_id')->constrained('pegawai')->onDelete('cascade'); // Pengendali Teknis
            $table->foreignId('ketua_tim_id')->constrained('pegawai')->onDelete('cascade'); // Ketua Tim
            $table->timestamps();
        });

        // Tabel untuk anggota tim (many-to-many relationship)
        Schema::create('pengawasan_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengawasan_id')->constrained('pengawasan')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate anggota in same pengawasan
            $table->unique(['pengawasan_id', 'pegawai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawasan_anggota');
        Schema::dropIfExists('pengawasan');
    }
};
