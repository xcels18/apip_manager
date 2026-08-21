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
            $table->dropUnique(['nip']);
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('nip', 30)->change();
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->unique('nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropUnique(['nip']);
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('nip', 18)->change();
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->unique('nip');
        });
    }
};
