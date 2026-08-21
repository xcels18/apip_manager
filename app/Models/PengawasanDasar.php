<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengawasanDasar extends Model
{
    protected $table = 'pengawasan_dasar';

    protected $fillable = [
        'pengawasan_id',
        'isi_dasar',
        'urutan',
    ];

    public function pengawasan()
    {
        return $this->belongsTo(Pengawasan::class);
    }
}
