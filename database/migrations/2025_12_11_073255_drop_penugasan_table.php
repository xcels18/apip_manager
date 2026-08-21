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
        Schema::dropIfExists('penugasan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['Audit', 'Reviu', 'Monitoring', 'Evaluasi', 'Perjalanan Dinas Luar Daerah']);
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi', 200)->nullable();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->enum('status', ['Direncanakan', 'Berlangsung', 'Selesai', 'Dibatalkan'])->default('Direncanakan');
            $table->timestamps();
        });
    }
};
