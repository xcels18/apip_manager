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
        Schema::create('pendidikan_formal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->string('nama_institusi', 200);
            $table->string('program_studi', 200)->nullable();
            $table->year('tahun_lulus');
            $table->string('nomor_ijazah', 100)->nullable();
            $table->string('file_ijazah')->nullable();
            $table->string('file_transkrip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikan_formal');
    }
};
