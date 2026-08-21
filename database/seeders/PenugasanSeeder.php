<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Penugasan;
use App\Models\Pegawai;
use Carbon\Carbon;

class PenugasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil pegawai pertama jika ada
        $pegawai = Pegawai::first();

        // Data penugasan untuk bulan Desember 2025
        $penugasan = [
            [
                'jenis' => 'Audit',
                'judul' => 'Audit Keuangan Dinas Pendidikan',
                'deskripsi' => 'Audit laporan keuangan Dinas Pendidikan tahun anggaran 2024',
                'tanggal_mulai' => '2025-12-10',
                'tanggal_selesai' => '2025-12-15',
                'lokasi' => 'Dinas Pendidikan Puncak Jaya',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Direncanakan',
            ],
            [
                'jenis' => 'Reviu',
                'judul' => 'Reviu Laporan Keuangan Semester I',
                'deskripsi' => 'Reviu laporan keuangan semester I tahun 2025',
                'tanggal_mulai' => '2025-12-05',
                'tanggal_selesai' => '2025-12-08',
                'lokasi' => 'Kantor Inspektorat',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Berlangsung',
            ],
            [
                'jenis' => 'Monitoring',
                'judul' => 'Monitoring Pelaksanaan Program Kesehatan',
                'deskripsi' => 'Monitoring pelaksanaan program kesehatan masyarakat',
                'tanggal_mulai' => '2025-12-18',
                'tanggal_selesai' => '2025-12-20',
                'lokasi' => 'Puskesmas Mulia',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Direncanakan',
            ],
            [
                'jenis' => 'Evaluasi',
                'judul' => 'Evaluasi Kinerja OPD Tahun 2024',
                'deskripsi' => 'Evaluasi kinerja seluruh OPD di lingkungan Pemkab Puncak Jaya',
                'tanggal_mulai' => '2025-12-22',
                'tanggal_selesai' => '2025-12-27',
                'lokasi' => 'Kantor Bupati',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Direncanakan',
            ],
            [
                'jenis' => 'Perjalanan Dinas Luar Daerah',
                'judul' => 'Bimtek APIP di Jakarta',
                'deskripsi' => 'Bimbingan teknis untuk peningkatan kapasitas APIP',
                'tanggal_mulai' => '2025-12-12',
                'tanggal_selesai' => '2025-12-14',
                'lokasi' => 'Jakarta',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Direncanakan',
            ],
            [
                'jenis' => 'Audit',
                'judul' => 'Audit Pengadaan Barang dan Jasa',
                'deskripsi' => 'Audit proses pengadaan barang dan jasa tahun 2024',
                'tanggal_mulai' => '2025-12-02',
                'tanggal_selesai' => '2025-12-04',
                'lokasi' => 'Bagian Pengadaan',
                'pegawai_id' => $pegawai?->id,
                'status' => 'Selesai',
            ],
        ];

        foreach ($penugasan as $p) {
            Penugasan::create($p);
        }
    }
}
