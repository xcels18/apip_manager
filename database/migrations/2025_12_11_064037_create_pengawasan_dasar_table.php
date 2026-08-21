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
        Schema::create('pengawasan_dasar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengawasan_id')->constrained('pengawasan')->onDelete('cascade');
            $table->text('isi_dasar'); // Isi dasar hukum
            $table->integer('urutan')->default(1); // Urutan dasar hukum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawasan_dasar');
    }
};
