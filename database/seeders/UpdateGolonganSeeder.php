<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping golongan lama ke format baru (Pangkat/Golongan)
        $golonganMapping = [
            'I/a' => 'Juru Muda/Ia',
            'I/b' => 'Juru Muda Tingkat I/Ib',
            'I/c' => 'Juru/Ic',
            'I/d' => 'Juru Tingkat I/Id',

            'II/a' => 'Pengatur Muda/IIa',
            'II/b' => 'Pengatur Muda Tingkat I/IIb',
            'II/c' => 'Pengatur/IIc',
            'II/d' => 'Pengatur Tingkat I/IId',

            'III/a' => 'Penata Muda/IIIa',
            'III/b' => 'Penata Muda Tingkat I/IIIb',
            'III/c' => 'Penata/IIIc',
            'III/d' => 'Penata Tingkat I/IIId',

            'IV/a' => 'Pembina/IVa',
            'IV/b' => 'Pembina Tingkat I/IVb',
            'IV/c' => 'Pembina Utama Muda/IVc',
            'IV/d' => 'Pembina Utama Madya/IVd',
            'IV/e' => 'Pembina Utama/IVe',
        ];

        // Update data yang sudah ada
        foreach ($golonganMapping as $old => $new) {
            DB::table('pegawai')
                ->where('golongan', $old)
                ->update(['golongan' => $new]);
        }

        $this->command->info('Golongan pegawai berhasil diupdate ke format Pangkat/Golongan!');
    }
}
