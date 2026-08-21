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
        Schema::create('diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->enum('jenis_diklat', ['Teknis', 'Fungsional', 'Kepemimpinan', 'Lainnya']);
            $table->string('nama_diklat', 200);
            $table->string('penyelenggara', 200);
            $table->year('tahun');
            $table->integer('jumlah_jam')->nullable();
            $table->string('nomor_sertifikat', 100)->nullable();
            $table->string('file_sertifikat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diklat');
    }
};
