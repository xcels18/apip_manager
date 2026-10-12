<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Employee IDs belong to the external directory, not the legacy local table.
        foreach (['pengawasan', 'pengawasan_anggota'] as $table) {
            foreach (Schema::getForeignKeys($table) as $foreign) {
                if ($foreign['foreign_table'] === 'pegawai') {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropForeign($foreign['columns']));
                }
            }
        }
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->string('nomor_st', 100)->nullable()->change();
            $table->index(['ketua_tim_id', 'tanggal_st'], 'pengawasan_ketua_tanggal_index');
            $table->index('tanggal_st');
        });
        Schema::table('pengawasan_anggota', fn (Blueprint $table) => $table->index(['pegawai_id', 'pengawasan_id'], 'anggota_pegawai_pengawasan_index'));
    }

    public function down(): void
    {
        // Refuse rollback if the legacy schema would invalidate new records.
        if (DB::table('pengawasan')->whereNull('nomor_st')->exists()) {
            throw new RuntimeException('Cannot restore legacy schema while draft surat tugas exist. Restore the pre-migration backup instead.');
        }
        $references = ['pengawasan' => ['penanggung_jawab_id', 'pengendali_teknis_id', 'ketua_tim_id'], 'pengawasan_anggota' => ['pegawai_id']];
        foreach ($references as $table => $columns) {
            foreach ($columns as $column) {
                if (DB::table($table)->whereNotNull($column)->whereNotIn($column, DB::table('pegawai')->select('id'))->exists()) {
                    throw new RuntimeException('Cannot restore legacy foreign keys while external employee IDs exist. Restore the pre-migration backup instead.');
                }
            }
        }
        foreach ($references as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->foreign($column)->references('id')->on('pegawai')->cascadeOnDelete();
                }
            });
        }
        Schema::table('pengawasan', fn (Blueprint $table) => $table->string('nomor_st', 100)->nullable(false)->change());
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropIndex('pengawasan_ketua_tanggal_index');
            $table->dropIndex(['tanggal_st']);
        });
        Schema::table('pengawasan_anggota', fn (Blueprint $table) => $table->dropIndex('anggota_pegawai_pengawasan_index'));
    }
};
