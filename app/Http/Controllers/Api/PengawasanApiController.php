<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PegawaiUnavailable;
use App\Http\Controllers\Controller;
use App\Http\Resources\PengawasanResource;
use App\Models\Pengawasan;
use App\Services\PenugasanService;
use App\Services\PersonilService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PengawasanApiController extends Controller
{
    public function __construct(private PersonilService $personil, private PenugasanService $penugasan) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => 'sometimes|in:belum_selesai,selesai', 'jenis_penugasan' => 'sometimes|string|max:100',
            'tahun' => 'sometimes|integer|between:1900,9999', 'bulan' => 'sometimes|integer|between:1,12',
            'per_page' => 'sometimes|integer|between:1,100', 'page' => 'sometimes|integer|min:1',
        ]);
        $query = Pengawasan::with('dasarHukum');
        foreach (['status', 'jenis_penugasan'] as $key) {
            if (isset($filters[$key])) {
                $query->where($key, $filters[$key]);
            }
        }
        if (isset($filters['tahun'])) {
            $query->whereYear('tanggal_st', $filters['tahun']);
        }
        if (isset($filters['bulan'])) {
            $query->whereMonth('tanggal_st', $filters['bulan']);
        }
        $records = $query->orderByDesc('tanggal_st')->orderByDesc('id')->paginate($filters['per_page'] ?? 15)->withQueryString();
        $this->personil->inject($records->items(), true);

        return PengawasanResource::collection($records);
    }

    public function show(string $id)
    {
        $record = Pengawasan::with('dasarHukum')->findOrFail($id);
        $this->personil->inject([$record], true);

        return response()->json(['success' => true, 'data' => new PengawasanResource($record)]);
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function update(Request $request, string $id)
    {
        return $this->save($request, Pengawasan::findOrFail($id));
    }

    private function save(Request $request, ?Pengawasan $existing = null)
    {
        $required = $existing ? 'sometimes|required' : 'required';
        $data = $request->validate([
            'nomor_st' => [$existing ? 'sometimes' : 'required', 'required', 'string', 'max:100', Rule::unique('pengawasan')->ignore($existing?->id)],
            'tanggal_st' => "$required|date",
            'lama_penugasan' => "$required|integer|min:1|max:366",
            'jenis_penugasan' => "$required|in:Audit,Reviu,Monitoring,Evaluasi,Pendampingan,Perjalanan Dinas Luar Daerah",
            'uraian_penugasan' => "$required|string", 'lokasi_penugasan' => "$required|string|max:200",
            'alat_angkut' => "$required|in:darat,udara", 'status' => "$required|in:belum_selesai,selesai",
            'penanggung_jawab_id' => 'nullable|integer|min:1', 'pengendali_teknis_id' => 'nullable|integer|min:1',
            'ketua_tim_id' => 'nullable|integer|min:1', 'anggota' => 'nullable|array', 'anggota.*' => 'required|integer|min:1|distinct',
            'dasar_hukum' => "$required|array|min:1", 'dasar_hukum.*' => 'required|string',
        ]);
        try {
            return DB::transaction(function () use ($request, $data, $existing) {
                $record = $existing ? Pengawasan::lockForUpdate()->findOrFail($existing->id) : new Pengawasan;
                $effective = array_replace($record->getAttributes(), $data);
                if (! array_key_exists('anggota', $data)) {
                    $effective['anggota'] = $record->exists ? DB::table('pengawasan_anggota')->where('pengawasan_id', $record->id)->pluck('pegawai_id')->all() : [];
                }
                $this->penugasan->validate($effective, $record->exists ? $record : null);
                $record->fill(Arr::except($data, ['anggota', 'dasar_hukum']));
                if (! $record->exists) {
                    $record->penandatangan_type = 'definitif';
                    $record->fill(app(\App\Services\SettingsService::class)->documentSnapshot());
                }
                $record->save();
                if (array_key_exists('anggota', $data)) {
                    DB::table('pengawasan_anggota')->where('pengawasan_id', $record->id)->delete();
                    foreach ($data['anggota'] ?? [] as $id) {
                        DB::table('pengawasan_anggota')->insert(['pengawasan_id' => $record->id, 'pegawai_id' => $id]);
                    }
                }
                if (array_key_exists('dasar_hukum', $data)) {
                    $record->dasarHukum()->delete();
                    foreach (array_values($data['dasar_hukum']) as $order => $text) {
                        $record->dasarHukum()->create(['isi_dasar' => $text, 'urutan' => $order + 1]);
                    }
                }
                $this->personil->snapshot($record);
                $record->load('dasarHukum');

                // Serialize while still inside the transaction, so response failures roll back too.
                return response()->json([
                    'success' => true,
                    'message' => $existing ? 'Pengawasan updated successfully' : 'Pengawasan created successfully',
                    'data' => (new PengawasanResource($record))->resolve($request),
                ], $existing ? 200 : 201);
            });
        } catch (ValidationException|PegawaiUnavailable $e) {
            throw $e;
        } catch (UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages(['nomor_st' => 'Nomor Surat Tugas sudah digunakan.']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Pengawasan gagal disimpan. Silakan coba kembali.'], 500);
        }
    }

    public function destroy(string $id)
    {
        $record = Pengawasan::findOrFail($id);
        DB::transaction(function () use ($record) {
            DB::table('pengawasan_anggota')->where('pengawasan_id', $record->id)->delete();
            $record->dasarHukum()->delete();
            $record->delete();
        });
        if ($record->file_laporan) {
            Storage::disk('public')->delete($record->file_laporan);
        }

        return response()->json(['success' => true, 'message' => 'Pengawasan deleted successfully']);
    }
}
