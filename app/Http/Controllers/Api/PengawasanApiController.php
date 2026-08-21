<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan;
use App\Http\Resources\PengawasanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengawasanApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengawasan::with([
            'penanggungJawab',
            'pengendaliTeknis',
            'ketuaTim',
            'anggota',
            'dasarHukum'
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis_penugasan
        if ($request->has('jenis_penugasan')) {
            $query->where('jenis_penugasan', $request->jenis_penugasan);
        }

        // Filter by year
        if ($request->has('tahun')) {
            $query->whereYear('tanggal_st', $request->tahun);
        }

        // Filter by month
        if ($request->has('bulan')) {
            $query->whereMonth('tanggal_st', $request->bulan);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $pengawasan = $query->orderBy('tanggal_st', 'desc')->paginate($perPage);

        return PengawasanResource::collection($pengawasan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_st' => 'required|string|max:255',
            'tanggal_st' => 'required|date',
            'lama_penugasan' => 'required|integer|min:1',
            'jenis_penugasan' => 'required|in:Audit,Reviu,Monitoring,Evaluasi,Pendampingan,Perjalanan Dinas Luar Daerah',
            'uraian_penugasan' => 'required|string',
            'lokasi_penugasan' => 'required|string',
            'alat_angkut' => 'required|in:darat,laut,udara',
            'status' => 'required|in:belum_selesai,selesai',
            'penanggung_jawab_id' => 'nullable|exists:pegawai,id',
            'pengendali_teknis_id' => 'nullable|exists:pegawai,id',
            'ketua_tim_id' => 'nullable|exists:pegawai,id',
            'anggota' => 'nullable|array',
            'anggota.*' => 'exists:pegawai,id',
            'dasar_hukum' => 'required|array|min:1',
            'dasar_hukum.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pengawasan = Pengawasan::create([
                'nomor_st' => $request->nomor_st,
                'tanggal_st' => $request->tanggal_st,
                'lama_penugasan' => $request->lama_penugasan,
                'jenis_penugasan' => $request->jenis_penugasan,
                'uraian_penugasan' => $request->uraian_penugasan,
                'lokasi_penugasan' => $request->lokasi_penugasan,
                'alat_angkut' => $request->alat_angkut,
                'status' => $request->status,
                'penanggung_jawab_id' => $request->penanggung_jawab_id,
                'pengendali_teknis_id' => $request->pengendali_teknis_id,
                'ketua_tim_id' => $request->ketua_tim_id,
            ]);

            // Attach anggota
            if ($request->has('anggota') && is_array($request->anggota)) {
                foreach ($request->anggota as $anggotaId) {
                    \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->insert([
                        'pengawasan_id' => $pengawasan->id,
                        'pegawai_id' => $anggotaId
                    ]);
                }
            }

            // Create dasar hukum
            if ($request->has('dasar_hukum')) {
                foreach ($request->dasar_hukum as $dasar) {
                    $pengawasan->dasarHukum()->create(['isi_dasar' => $dasar]);
                }
            }

            $pengawasan->load(['penanggungJawab', 'pengendaliTeknis', 'ketuaTim', 'anggota', 'dasarHukum']);

            return response()->json([
                'success' => true,
                'message' => 'Pengawasan created successfully',
                'data' => new PengawasanResource($pengawasan),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pengawasan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengawasan = Pengawasan::with([
            'penanggungJawab',
            'pengendaliTeknis',
            'ketuaTim',
            'anggota',
            'dasarHukum'
        ])->find($id);

        if (!$pengawasan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengawasan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PengawasanResource($pengawasan),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengawasan = Pengawasan::find($id);

        if (!$pengawasan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengawasan not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomor_st' => 'sometimes|required|string|max:255',
            'tanggal_st' => 'sometimes|required|date',
            'lama_penugasan' => 'sometimes|required|integer|min:1',
            'jenis_penugasan' => 'sometimes|required|in:Audit,Reviu,Monitoring,Evaluasi,Pendampingan,Perjalanan Dinas Luar Daerah',
            'uraian_penugasan' => 'sometimes|required|string',
            'lokasi_penugasan' => 'sometimes|required|string',
            'alat_angkut' => 'sometimes|required|in:darat,laut,udara',
            'status' => 'sometimes|required|in:belum_selesai,selesai',
            'penanggung_jawab_id' => 'nullable|exists:pegawai,id',
            'pengendali_teknis_id' => 'nullable|exists:pegawai,id',
            'ketua_tim_id' => 'nullable|exists:pegawai,id',
            'anggota' => 'nullable|array',
            'anggota.*' => 'exists:pegawai,id',
            'dasar_hukum' => 'sometimes|required|array|min:1',
            'dasar_hukum.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pengawasan->update($request->only([
                'nomor_st',
                'tanggal_st',
                'lama_penugasan',
                'jenis_penugasan',
                'uraian_penugasan',
                'lokasi_penugasan',
                'alat_angkut',
                'status',
                'penanggung_jawab_id',
                'pengendali_teknis_id',
                'ketua_tim_id',
            ]));

            // Update anggota
            if ($request->has('anggota') && is_array($request->anggota)) {
                \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->where('pengawasan_id', $pengawasan->id)->delete();
                foreach ($request->anggota as $anggotaId) {
                    \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->insert([
                        'pengawasan_id' => $pengawasan->id,
                        'pegawai_id' => $anggotaId
                    ]);
                }
            }

            // Update dasar hukum
            if ($request->has('dasar_hukum')) {
                $pengawasan->dasarHukum()->delete();
                foreach ($request->dasar_hukum as $dasar) {
                    $pengawasan->dasarHukum()->create(['isi_dasar' => $dasar]);
                }
            }

            $pengawasan->load(['penanggungJawab', 'pengendaliTeknis', 'ketuaTim', 'anggota', 'dasarHukum']);

            return response()->json([
                'success' => true,
                'message' => 'Pengawasan updated successfully',
                'data' => new PengawasanResource($pengawasan),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pengawasan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengawasan = Pengawasan::find($id);

        if (!$pengawasan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengawasan not found',
            ], 404);
        }

        try {
            $pengawasan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengawasan deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pengawasan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
