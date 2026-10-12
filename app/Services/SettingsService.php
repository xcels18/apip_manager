<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SettingsService
{
    private ?array $values = null;

    public function all(): array
    {
        return $this->values ??= DB::table('system_settings')->pluck('value', 'key')->all();
    }

    public function documentSnapshot(): array
    {
        $defaults = [
            'penandatangan_definitif_nama' => ['definitif_nama', 'BOTTEN TANDIPADA'],
            'penandatangan_definitif_nip' => ['definitif_nip', '196612141995031001'],
            'penandatangan_definitif_jabatan' => ['definitif_jabatan', 'Plt. Inspektur'],
            'kop_pemerintah' => ['kop_pemerintah', 'PEMERINTAH KABUPATEN PUNCAK JAYA'],
            'kop_instansi' => ['kop_instansi', 'INSPEKTORAT'],
            'kop_jalan' => ['kop_jalan', 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015'],
            'kop_email' => ['kop_email', 'Email: inspektorat@puncakjayakab.go.id'],
        ];
        $snapshot = [];
        foreach ($defaults as $column => [$key, $default]) {
            $snapshot[$column] = $this->all()[$key] ?? $default;
        }

        return $snapshot;
    }

    public function forget(): void
    {
        $this->values = null;
    }
}
