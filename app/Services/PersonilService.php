<?php

namespace App\Services;

use App\Exceptions\PegawaiUnavailable;
use App\Models\Pengawasan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersonilService
{
    private const ROLES = ['penanggungJawab' => 'penanggung_jawab_id', 'pengendaliTeknis' => 'pengendali_teknis_id', 'ketuaTim' => 'ketua_tim_id'];

    public function __construct(private PegawaiService $pegawai) {}

    public function inject(iterable $records, bool $strict = false): void
    {
        $records = collect($records);
        $live = $records->filter(fn ($p) => ! $p->personil_snapshot);
        $pivot = $live->isEmpty() ? collect() : DB::table('pengawasan_anggota')
            ->whereIn('pengawasan_id', $live->pluck('id'))->get()->groupBy('pengawasan_id');
        $needsEmployees = $live->contains(fn ($p) => $p->penanggung_jawab_id || $p->pengendali_teknis_id || $p->ketua_tim_id)
            || $pivot->isNotEmpty();
        $employees = collect();
        if ($needsEmployees) {
            try {
                $employees = $this->pegawai->all();
            } catch (PegawaiUnavailable $e) {
                if ($strict) {
                    throw $e;
                }
                if (request()->hasSession()) {
                    session()->flash('error', $e->getMessage());
                }
            }
        }
        foreach ($records as $p) {
            $snapshot = $p->personil_snapshot ? json_decode($p->personil_snapshot, true) : null;
            foreach (self::ROLES as $role => $column) {
                $value = $snapshot ? ($snapshot[$role] ?? null) : $employees->get($p->$column);
                $p->setRelation($role, $value ? (object) $value : null);
            }
            $members = $snapshot ? collect($snapshot['anggota'] ?? [])->map(fn ($a) => (object) $a)
                : ($pivot->get($p->id, collect())->map(fn ($a) => $employees->get($a->pegawai_id))->filter()->values());
            $p->setRelation('anggota', $members);
        }
    }

    public function snapshot(Pengawasan $p): void
    {
        $old = json_decode($p->personil_snapshot ?? '{}', true) ?: [];
        $historical = collect(array_filter(array_merge(
            array_map(fn ($role) => $old[$role] ?? null, array_keys(self::ROLES)), $old['anggota'] ?? []
        )))->keyBy('id');
        $members = DB::table('pengawasan_anggota')->where('pengawasan_id', $p->id)->pluck('pegawai_id');
        $ids = collect(array_map(fn ($column) => $p->$column, self::ROLES))->merge($members)->filter()->unique();
        $employees = $ids->diff($historical->keys())->isEmpty() ? collect() : $this->pegawai->all();
        $resolve = function ($id) use ($historical, $employees) {
            if (! $id) {
                return null;
            }
            $value = $historical->get($id) ?? $employees->get($id);
            if (! $value) {
                throw ValidationException::withMessages(['personil' => "Pegawai dengan ID {$id} tidak ditemukan pada layanan pegawai."]);
            }

            return (object) $value;
        };
        $snapshot = [];
        foreach (self::ROLES as $role => $column) {
            $snapshot[$role] = $resolve($p->$column);
        }
        $snapshot['anggota'] = $members->map($resolve)->values()->all();
        if ($p->jenis_penugasan === 'Perjalanan Dinas Luar Daerah') {
            $people = collect(array_filter(array_merge(array_values($snapshot), $snapshot['anggota'])))->filter(fn ($a) => is_object($a));
            $inspektur = \App\Models\SystemSetting::valueFor('definitif_nama', 'BOTTEN TANDIPADA');
            if ($people->count() > 1 && $people->contains(fn ($a) => strcasecmp($a->nama, $inspektur) === 0)) {
                throw ValidationException::withMessages(['personil' => 'Penandatangan definitif harus melakukan perjalanan dinas dalam Surat Tugas tersendiri.']);
            }
        }
        $snapshot['plh'] = null;
        if ($p->penandatangan_type === 'plh') {
            $plh = $old['plh'] ?? null;
            if ($plh && $plh['nama'] === $p->penandatangan_plh_nama && $plh['jabatan'] === $p->penandatangan_plh_jabatan) {
                $snapshot['plh'] = $plh;
            } else {
                $snapshot['plh'] = $this->pegawai->all()->first(fn ($a) => $a->nama === $p->penandatangan_plh_nama
                    && ($a->jabatan ?? null) === $p->penandatangan_plh_jabatan);
                if (! $snapshot['plh']) {
                    throw ValidationException::withMessages(['penandatangan_plh_nama' => 'Pegawai Plh. tidak ditemukan.']);
                }
            }
        }
        $p->personil_snapshot = json_encode($snapshot, JSON_THROW_ON_ERROR);
        $p->save();
        $this->inject([$p]);
    }
}
