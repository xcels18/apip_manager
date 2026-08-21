<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengawasan extends Model
{
    protected $table = 'pengawasan';

    protected $fillable = [
        'nomor_st',
        'tanggal_st',
        'lama_penugasan',
        'uraian_penugasan',
        'lokasi_penugasan',
        'alat_angkut',
        'jenis_penugasan',
        'status',
        'file_laporan',
        'penanggung_jawab_id',
        'pengendali_teknis_id',
        'ketua_tim_id',
        'penandatangan_type',
        'penandatangan_plh_nama',
        'penandatangan_plh_jabatan',
    ];

    protected $casts = [
        'tanggal_st' => 'date',
        'lama_penugasan' => 'integer',
    ];

    public function dasarHukum()
    {
        return $this->hasMany(PengawasanDasar::class)->orderBy('urutan');
    }

    // Helper method to get all personil in this pengawasan
    public function getAllPersonil()
    {
        $personil = collect([
            $this->penanggung_jawab_id,
            $this->pengendali_teknis_id,
            $this->ketua_tim_id,
        ]);

        // Add anggota from pivot table directly
        $anggotaIds = \Illuminate\Support\Facades\DB::table('pengawasan_anggota')
            ->where('pengawasan_id', $this->id)
            ->pluck('pegawai_id');
            
        $personil = $personil->merge($anggotaIds);

        return $personil->unique()->filter();
    }

    // Accessor for status label
    public function getStatusLabelAttribute()
    {
        return $this->status === 'selesai' ? 'Selesai' : 'Belum Selesai';
    }

    // Accessor for status badge color
    public function getStatusBadgeColorAttribute()
    {
        return $this->status === 'selesai' ? 'success' : 'warning';
    }

    // Accessor for status badge class
    public function getStatusBadgeClassAttribute()
    {
        return $this->status === 'selesai'
            ? 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;'
            : 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;';
    }

    // Accessor for alat angkut label
    public function getAlatAngkutLabelAttribute()
    {
        return $this->alat_angkut === 'udara' ? 'Transportasi Udara' : 'Transportasi Darat';
    }
}
