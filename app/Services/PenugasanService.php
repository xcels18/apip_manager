<?php

namespace App\Services;

use App\Models\Pengawasan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenugasanService
{
    public function conflict($pegawaiId, Carbon $start, Carbon $end, $excludeId = null, array $roles = ['ketua_tim', 'anggota']): ?array
    {
        if (! array_intersect($roles, ['ketua_tim', 'anggota'])) {
            return null;
        }
        $member = DB::table('pengawasan_anggota')->selectRaw('1')
            ->whereColumn('pengawasan_id', 'pengawasan.id')->where('pegawai_id', $pegawaiId)->limit(1);
        $query = Pengawasan::query()->select('pengawasan.*')->selectSub($member, 'is_member')
            ->where('tanggal_st', '<=', $end->toDateString())
            ->when($excludeId, fn ($q) => $q->where('pengawasan.id', '!=', $excludeId))
            ->where(function ($q) use ($roles, $pegawaiId, $member) {
                if (in_array('ketua_tim', $roles)) {
                    $q->where('ketua_tim_id', $pegawaiId);
                }
                if (in_array('anggota', $roles)) {
                    $q->orWhereExists($member);
                }
            });

        // Only assignments involving this employee are read; no per-row pivot queries.
        foreach ($query->lazyById(200, 'pengawasan.id', 'id') as $p) {
            $existingStart = Carbon::parse($p->tanggal_st);
            $existingEnd = $existingStart->copy()->addDays($p->lama_penugasan - 1);
            if ($start->lte($existingEnd)) {
                return [
                    'nomor_st' => $p->nomor_st,
                    'tanggal_mulai' => $existingStart->format('d/m/Y'),
                    'tanggal_selesai' => $existingEnd->format('d/m/Y'),
                    'jenis_penugasan' => $p->jenis_penugasan,
                    'role' => in_array('anggota', $roles) && $p->is_member ? 'Anggota' : 'Ketua Tim',
                ];
            }
        }

        return null;
    }

    public function validate(array $data, ?Pengawasan $existing = null): void
    {
        $members = $data['anggota'] ?? [];
        $people = collect([$data['penanggung_jawab_id'] ?? null, $data['pengendali_teknis_id'] ?? null, $data['ketua_tim_id'] ?? null])
            ->merge($members)->filter();
        if ($people->count() !== $people->unique()->count()) {
            throw ValidationException::withMessages(['personil' => 'Personil tidak boleh duplikat dalam satu Surat Tugas.']);
        }
        $travel = $data['jenis_penugasan'] === 'Perjalanan Dinas Luar Daerah';
        if (! $travel && (empty($data['penanggung_jawab_id']) || empty($data['ketua_tim_id']))) {
            throw ValidationException::withMessages(['personil' => 'Penanggung jawab dan ketua tim wajib dipilih.']);
        }
        if (($data['status'] ?? 'belum_selesai') === 'selesai' && ! $existing?->file_laporan) {
            throw ValidationException::withMessages(['status' => 'Unggah laporan melalui aplikasi sebelum mengubah status menjadi selesai.']);
        }
        $start = Carbon::parse($data['tanggal_st']);
        $end = $start->copy()->addDays($data['lama_penugasan'] - 1);
        $checked = collect($members);
        if (! $travel && ! empty($data['ketua_tim_id'])) {
            $checked->push($data['ketua_tim_id']);
        }
        foreach ($checked as $id) {
            if ($conflict = $this->conflict($id, $start, $end, $existing?->id)) {
                throw ValidationException::withMessages(['personil' => "Pegawai {$id} sudah bertugas pada {$conflict['nomor_st']} ({$conflict['tanggal_mulai']} - {$conflict['tanggal_selesai']})."]);
            }
        }
    }
}
