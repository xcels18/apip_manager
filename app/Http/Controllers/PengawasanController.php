<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawasan;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengawasanController extends Controller
{
    public function injectPegawaiData($pengawasanList)
    {
        $pegawaiIds = [];
        foreach ($pengawasanList as $p) {
            if ($p->personil_snapshot) {
                $snapshot = json_decode($p->personil_snapshot);
                $p->penanggungJawab = $snapshot->penanggungJawab ?? null;
                $p->pengendaliTeknis = $snapshot->pengendaliTeknis ?? null;
                $p->ketuaTim = $snapshot->ketuaTim ?? null;
                $p->anggota = collect($snapshot->anggota ?? []);
                continue;
            }

            if ($p->penanggung_jawab_id) $pegawaiIds[] = $p->penanggung_jawab_id;
            if ($p->pengendali_teknis_id) $pegawaiIds[] = $p->pengendali_teknis_id;
            if ($p->ketua_tim_id) $pegawaiIds[] = $p->ketua_tim_id;
            $anggotaIds = \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->where('pengawasan_id', $p->id)->pluck('pegawai_id')->toArray();
            $p->anggota_ids = $anggotaIds;
            $pegawaiIds = array_merge($pegawaiIds, $anggotaIds);
        }
        
        $pegawaiIds = array_unique($pegawaiIds);
        
        if (!empty($pegawaiIds)) {
            $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
                ->get(config('services.pegawai.url'), ['per_page' => 1000]);
                
            $pegawaiMap = [];
            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                foreach ($data as $item) {
                    $pegawaiMap[$item['id']] = (object) $item;
                }
            }
            
            foreach ($pengawasanList as $p) {
                if ($p->personil_snapshot) continue;

                $p->penanggungJawab = $p->penanggung_jawab_id && isset($pegawaiMap[$p->penanggung_jawab_id]) ? $pegawaiMap[$p->penanggung_jawab_id] : null;
                $p->pengendaliTeknis = $p->pengendali_teknis_id && isset($pegawaiMap[$p->pengendali_teknis_id]) ? $pegawaiMap[$p->pengendali_teknis_id] : null;
                $p->ketuaTim = $p->ketua_tim_id && isset($pegawaiMap[$p->ketua_tim_id]) ? $pegawaiMap[$p->ketua_tim_id] : null;
                
                $anggota = [];
                foreach ($p->anggota_ids as $aId) {
                    if (isset($pegawaiMap[$aId])) {
                        $anggota[] = $pegawaiMap[$aId];
                    }
                }
                $p->anggota = collect($anggota);
            }
        }
    }

    private function createPersonilSnapshot($pengawasan)
    {
        $existingSnapshot = $pengawasan->personil_snapshot ? json_decode($pengawasan->personil_snapshot) : null;
        
        // Temporarily clear the snapshot to force fetching from API
        $pengawasan->personil_snapshot = null;
        $this->injectPegawaiData([$pengawasan]);

        // Merge existing snapshot data to prevent overwriting historical data on edit
        if ($existingSnapshot) {
            if ($existingSnapshot->penanggungJawab && $pengawasan->penanggung_jawab_id == $existingSnapshot->penanggungJawab->id) {
                $pengawasan->penanggungJawab = $existingSnapshot->penanggungJawab;
            }
            if ($existingSnapshot->pengendaliTeknis && $pengawasan->pengendali_teknis_id == $existingSnapshot->pengendaliTeknis->id) {
                $pengawasan->pengendaliTeknis = $existingSnapshot->pengendaliTeknis;
            }
            if ($existingSnapshot->ketuaTim && $pengawasan->ketua_tim_id == $existingSnapshot->ketuaTim->id) {
                $pengawasan->ketuaTim = $existingSnapshot->ketuaTim;
            }
            
            $snapshotAnggotaMap = [];
            foreach ($existingSnapshot->anggota ?? [] as $a) {
                $snapshotAnggotaMap[$a->id] = $a;
            }
            
            $mergedAnggota = [];
            foreach ($pengawasan->anggota as $newA) {
                if (isset($snapshotAnggotaMap[$newA->id])) {
                    $mergedAnggota[] = $snapshotAnggotaMap[$newA->id];
                } else {
                    $mergedAnggota[] = $newA;
                }
            }
            $pengawasan->anggota = collect($mergedAnggota);
        }

        $plhData = null;
        if ($pengawasan->penandatangan_type === 'plh') {
            $plhData = $this->resolvePegawaiPlh($pengawasan);
            
            // Keep old PLH snapshot if name and jabatan haven't changed
            if ($existingSnapshot && isset($existingSnapshot->plh) && $existingSnapshot->plh && 
                $existingSnapshot->plh->nama == $pengawasan->penandatangan_plh_nama && 
                $existingSnapshot->plh->jabatan == $pengawasan->penandatangan_plh_jabatan) {
                $plhData = $existingSnapshot->plh;
            }
        } else {
            $plhData = null;
        }

        $snapshot = [
            'penanggungJawab' => $pengawasan->penanggungJawab,
            'pengendaliTeknis' => $pengawasan->pengendaliTeknis,
            'ketuaTim' => $pengawasan->ketuaTim,
            'anggota' => $pengawasan->anggota,
            'plh' => $plhData,
        ];

        // Update database directly so we don't save dynamically attached properties like penanggungJawab
        \Illuminate\Support\Facades\DB::table('pengawasan')
            ->where('id', $pengawasan->id)
            ->update([
                'personil_snapshot' => json_encode($snapshot),
                'updated_at' => now(),
            ]);
            
        $pengawasan->personil_snapshot = json_encode($snapshot);
    }

    private function resolvePegawaiPlh($pengawasan)
    {
        if ($pengawasan->personil_snapshot) {
            $snapshot = json_decode($pengawasan->personil_snapshot);
            if (isset($snapshot->plh) && $snapshot->plh) {
                return $snapshot->plh;
            }
        }

        if (($pengawasan->penandatangan_type ?? 'definitif') === 'plh' && $pengawasan->penandatangan_plh_nama) {
             $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
                ->get(config('services.pegawai.url'), [
                    'search' => $pengawasan->penandatangan_plh_nama,
                    'per_page' => 1
                ]);
             if ($response->successful() && !empty($response->json()['data'])) {
                 return (object) $response->json()['data'][0];
             }
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengawasan::query();

        if ($request->filled('status') && in_array($request->status, ['belum_selesai', 'selesai'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_penugasan', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_st', 'like', "%{$search}%")
                  ->orWhere('uraian_penugasan', 'like', "%{$search}%")
                  ->orWhere('lokasi_penugasan', 'like', "%{$search}%");
            });
        }

        $pengawasan = $query->orderBy('tanggal_st', 'desc')->paginate(12)->withQueryString();
        
        $this->injectPegawaiData($pengawasan->items());

        $totalSemua = Pengawasan::count();
        $totalBelumSelesai = Pengawasan::where('status', 'belum_selesai')->count();
        $totalSelesai = Pengawasan::where('status', 'selesai')->count();

        $jenisList = Pengawasan::select('jenis_penugasan')->distinct()->orderBy('jenis_penugasan')->pluck('jenis_penugasan');

        return view('pengawasan.index', compact('pengawasan', 'totalSemua', 'totalBelumSelesai', 'totalSelesai', 'jenisList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
            ->get(config('services.pegawai.url'), ['per_page' => 1000]);
        $pegawai = $response->successful() ? collect($response->json()['data'])->map(fn($p) => (object)$p) : collect();
        $tahun = date('Y');

        return view('pengawasan.create', compact('pegawai', 'tahun'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tahun = date('Y');

        // Check if Perjalanan Dinas Luar Daerah
        $isPerjalananDinas = $request->jenis_penugasan === 'Perjalanan Dinas Luar Daerah';

        $validated = $request->validate([
            'nomor_st_number' => 'nullable|string|max:50',
            'tanggal_st' => 'required|date',
            'lama_penugasan' => 'required|integer|min:1',
            'uraian_penugasan' => 'required|string',
            'lokasi_penugasan' => 'required|string|max:200',
            'alat_angkut' => 'required|in:darat,udara',
            'jenis_penugasan' => 'required|string|max:100',
            'dasar_hukum' => 'required|array|min:1',
            'dasar_hukum.*' => 'required|string',
            'penanggung_jawab_id' => $isPerjalananDinas ? 'nullable' : 'required|integer',
            'pengendali_teknis_id' => 'nullable|integer',
            'ketua_tim_id' => $isPerjalananDinas ? 'nullable' : 'required|integer',
            'anggota' => 'nullable|array',
            'anggota.*' => 'integer',
            'penandatangan_type' => 'required|in:definitif,plh',
            'penandatangan_plh_nama' => 'required_if:penandatangan_type,plh|nullable|string|max:200',
            'penandatangan_plh_jabatan' => 'required_if:penandatangan_type,plh|nullable|string|max:200',
        ], [
            'tanggal_st.required' => 'Tanggal Surat Tugas wajib diisi',
            'lama_penugasan.required' => 'Lama Penugasan wajib diisi',
            'lama_penugasan.min' => 'Lama Penugasan minimal 1 hari',
            'uraian_penugasan.required' => 'Uraian Penugasan wajib diisi',
            'lokasi_penugasan.required' => 'Lokasi Penugasan wajib diisi',
            'alat_angkut.required' => 'Alat Angkut wajib dipilih',
            'jenis_penugasan.required' => 'Jenis Penugasan wajib diisi',
            'dasar_hukum.required' => 'Dasar Hukum wajib diisi',
            'dasar_hukum.min' => 'Minimal harus ada 1 Dasar Hukum',
            'dasar_hukum.*.required' => 'Dasar Hukum tidak boleh kosong',
            'penanggung_jawab_id.required' => 'Penanggung Jawab wajib dipilih',
            'ketua_tim_id.required' => 'Ketua Tim wajib dipilih',
            'penandatangan_type.required' => 'Jenis penandatangan wajib dipilih',
            'penandatangan_plh_nama.required_if' => 'Nama Plh. wajib diisi jika memilih Plh.',
            'penandatangan_plh_jabatan.required_if' => 'Jabatan Plh. wajib diisi jika memilih Plh.',
        ]);

        // Build full nomor ST (only if nomor_st_number is provided)
        $nomorST = null;
        if (!empty($validated['nomor_st_number'])) {
            $nomorST = '100.3.5.4/' . $validated['nomor_st_number'] . '/ST/ITKAB/' . $tahun;
        }

        // Check for duplicate personil (filter out null values)
        $personil = collect([
            $validated['penanggung_jawab_id'] ?? null,
            $validated['pengendali_teknis_id'] ?? null,
            $validated['ketua_tim_id'] ?? null,
        ])->filter();

        if (isset($validated['anggota'])) {
            $personil = $personil->merge($validated['anggota']);
        }

        if ($personil->count() !== $personil->unique()->count()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Personil tidak boleh duplikat dalam satu Surat Tugas!');
        }

        // Check if Perjalanan Dinas with Plt. Inspektur - only one person allowed
        if ($isPerjalananDinas && $personil->count() > 0) {
            $allPersonilIds = $personil->toArray();
            $hasPltInspektur = false;
            foreach ($allPersonilIds as $pid) {
                $nama = $this->getPegawaiNameFromApi($pid);
                if (stripos($nama, 'BOTTEN TANDIPADA') !== false) {
                    $hasPltInspektur = true;
                    break;
                }
            }

            if ($hasPltInspektur && $personil->count() > 1) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jika Plt. Inspektur dipilih untuk Perjalanan Dinas Luar Daerah, tidak boleh ada pegawai lain yang dipilih!');
            }
        }

        // Check if Ketua Tim or Anggota are already assigned in overlapping dates
        $tanggalMulai = Carbon::parse($validated['tanggal_st']);
        $tanggalSelesai = $tanggalMulai->copy()->addDays($validated['lama_penugasan'] - 1);

        // Check Ketua Tim availability (check both as ketua_tim AND anggota) - only if not Perjalanan Dinas
        if (!$isPerjalananDinas && isset($validated['ketua_tim_id']) && $validated['ketua_tim_id']) {
            $ketuaTimConflict = $this->checkPersonilConflict(
                $validated['ketua_tim_id'],
                $tanggalMulai,
                $tanggalSelesai,
                null, // no ID for new record
                ['ketua_tim', 'anggota'] // Check both roles
            );

            if ($ketuaTimConflict) {
                $pegawaiName = $this->getPegawaiNameFromApi($validated['ketua_tim_id']);
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Ketua Tim ({$pegawaiName}) sudah bertugas sebagai {$ketuaTimConflict['role']} di rentang tanggal {$ketuaTimConflict['tanggal_mulai']} - {$ketuaTimConflict['tanggal_selesai']} pada pengawasan: {$ketuaTimConflict['nomor_st']}");
            }
        }

        // Check Anggota availability (check both as anggota AND ketua_tim)
        if (isset($validated['anggota']) && count($validated['anggota']) > 0) {
            foreach ($validated['anggota'] as $anggotaId) {
                if (!$anggotaId) continue; // Skip if null or empty

                $anggotaConflict = $this->checkPersonilConflict(
                    $anggotaId,
                    $tanggalMulai,
                    $tanggalSelesai,
                    null, // no ID for new record
                    ['ketua_tim', 'anggota'] // Check both roles
                );

                if ($anggotaConflict) {
                    $pegawaiName = $this->getPegawaiNameFromApi($anggotaId);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Anggota ({$pegawaiName}) sudah bertugas sebagai {$anggotaConflict['role']} di rentang tanggal {$anggotaConflict['tanggal_mulai']} - {$anggotaConflict['tanggal_selesai']} pada pengawasan: {$anggotaConflict['nomor_st']}");
                }
            }
        }

        try {
            DB::beginTransaction();

            $definitifNama = \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTEN TANDIPADA';
            $definitifNip = \App\Models\SystemSetting::where('key', 'definitif_nip')->first()->value ?? '196612141995031001';
            $definitifJabatan = \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. Inspektur';
            $kopPemerintah = \App\Models\SystemSetting::where('key', 'kop_pemerintah')->first()->value ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
            $kopInstansi = \App\Models\SystemSetting::where('key', 'kop_instansi')->first()->value ?? 'INSPEKTORAT';
            $kopJalan = \App\Models\SystemSetting::where('key', 'kop_jalan')->first()->value ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
            $kopEmail = \App\Models\SystemSetting::where('key', 'kop_email')->first()->value ?? 'Email: inspektorat@puncakjayakab.go.id';

            $pengawasan = Pengawasan::create([
                'nomor_st' => $nomorST,
                'tanggal_st' => $validated['tanggal_st'],
                'lama_penugasan' => $validated['lama_penugasan'],
                'uraian_penugasan' => $validated['uraian_penugasan'],
                'lokasi_penugasan' => $validated['lokasi_penugasan'],
                'alat_angkut' => $validated['alat_angkut'],
                'jenis_penugasan' => $validated['jenis_penugasan'],
                'status' => 'belum_selesai', // Default status
                'penanggung_jawab_id' => $validated['penanggung_jawab_id'] ?? null,
                'pengendali_teknis_id' => $validated['pengendali_teknis_id'] ?? null,
                'ketua_tim_id' => $validated['ketua_tim_id'] ?? null,
                'penandatangan_type' => $validated['penandatangan_type'],
                'penandatangan_plh_nama' => $validated['penandatangan_type'] === 'plh' ? $validated['penandatangan_plh_nama'] : null,
                'penandatangan_plh_jabatan' => $validated['penandatangan_type'] === 'plh' ? $validated['penandatangan_plh_jabatan'] : null,
                'penandatangan_definitif_nama' => $definitifNama,
                'penandatangan_definitif_nip' => $definitifNip,
                'penandatangan_definitif_jabatan' => $definitifJabatan,
                'kop_pemerintah' => $kopPemerintah,
                'kop_instansi' => $kopInstansi,
                'kop_jalan' => $kopJalan,
                'kop_email' => $kopEmail,
            ]);

            // Attach anggota
            if (isset($validated['anggota'])) {
                foreach ($validated['anggota'] as $anggotaId) {
                    \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->insert([
                        'pengawasan_id' => $pengawasan->id,
                        'pegawai_id' => $anggotaId
                    ]);
                }
            }

            // Save dasar hukum
            if (isset($validated['dasar_hukum']) && count($validated['dasar_hukum']) > 0) {
                foreach ($validated['dasar_hukum'] as $index => $dasar) {
                    $pengawasan->dasarHukum()->create([
                        'isi_dasar' => $dasar,
                        'urutan' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            $this->createPersonilSnapshot($pengawasan);

            return redirect()->route('pengawasan.index')
                ->with('success', 'Data Pengawasan berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])
            ->findOrFail($id);
        
        $this->injectPegawaiData([$pengawasan]);

        return view('pengawasan.show', compact('pengawasan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])
            ->findOrFail($id);
            
        $this->injectPegawaiData([$pengawasan]);

        $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
            ->get(config('services.pegawai.url'), ['per_page' => 1000]);
        $pegawai = $response->successful() ? collect($response->json()['data'])->map(fn($p) => (object)$p) : collect();
        $tahun = date('Y');

        // Extract nomor from full nomor_st
        // Format: 100.3.5.4/XXX/ST/ITKAB/2025
        $nomorParts = explode('/', $pengawasan->nomor_st);
        $nomorStNumber = $nomorParts[1] ?? '';

        return view('pengawasan.edit', compact('pengawasan', 'pegawai', 'tahun', 'nomorStNumber'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengawasan = Pengawasan::findOrFail($id);
        $tahun = date('Y');

        // Check if Perjalanan Dinas Luar Daerah
        $isPerjalananDinas = $request->jenis_penugasan === 'Perjalanan Dinas Luar Daerah';

        $validated = $request->validate([
            'nomor_st_number' => 'nullable|string|max:50',
            'tanggal_st' => 'required|date',
            'lama_penugasan' => 'required|integer|min:1',
            'uraian_penugasan' => 'required|string',
            'lokasi_penugasan' => 'required|string|max:200',
            'alat_angkut' => 'required|in:darat,udara',
            'jenis_penugasan' => 'required|string|max:100',
            'dasar_hukum' => 'required|array|min:1',
            'dasar_hukum.*' => 'required|string',
            'penanggung_jawab_id' => $isPerjalananDinas ? 'nullable' : 'required|integer',
            'pengendali_teknis_id' => 'nullable|integer',
            'ketua_tim_id' => $isPerjalananDinas ? 'nullable' : 'required|integer',
            'anggota' => 'nullable|array',
            'anggota.*' => 'integer',
            'penandatangan_type' => 'required|in:definitif,plh',
            'penandatangan_plh_nama' => 'required_if:penandatangan_type,plh|nullable|string|max:200',
            'penandatangan_plh_jabatan' => 'required_if:penandatangan_type,plh|nullable|string|max:200',
        ], [
            'tanggal_st.required' => 'Tanggal Surat Tugas wajib diisi',
            'lama_penugasan.required' => 'Lama Penugasan wajib diisi',
            'lama_penugasan.min' => 'Lama Penugasan minimal 1 hari',
            'uraian_penugasan.required' => 'Uraian Penugasan wajib diisi',
            'lokasi_penugasan.required' => 'Lokasi Penugasan wajib diisi',
            'jenis_penugasan.required' => 'Jenis Penugasan wajib diisi',
            'dasar_hukum.required' => 'Dasar Hukum wajib diisi',
            'dasar_hukum.min' => 'Minimal harus ada 1 Dasar Hukum',
            'dasar_hukum.*.required' => 'Dasar Hukum tidak boleh kosong',
            'penanggung_jawab_id.required' => 'Penanggung Jawab wajib dipilih',
            'ketua_tim_id.required' => 'Ketua Tim wajib dipilih',
            'penandatangan_type.required' => 'Jenis penandatangan wajib dipilih',
            'penandatangan_plh_nama.required_if' => 'Nama Plh. wajib diisi jika memilih Plh.',
            'penandatangan_plh_jabatan.required_if' => 'Jabatan Plh. wajib diisi jika memilih Plh.',
        ]);



        // Build full nomor ST (only if nomor_st_number is provided)
        $nomorST = null;
        if (!empty($validated['nomor_st_number'])) {
            $nomorST = '100.3.5.4/' . $validated['nomor_st_number'] . '/ST/ITKAB/' . $tahun;
        }

        // Check for duplicate personil (filter out null values)
        $personil = collect([
            $validated['penanggung_jawab_id'] ?? null,
            $validated['pengendali_teknis_id'] ?? null,
            $validated['ketua_tim_id'] ?? null,
        ])->filter();

        if (isset($validated['anggota'])) {
            $personil = $personil->merge($validated['anggota']);
        }

        if ($personil->count() !== $personil->unique()->count()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Personil tidak boleh duplikat dalam satu Surat Tugas!');
        }

        // Check if Perjalanan Dinas with Plt. Inspektur - only one person allowed
        if ($isPerjalananDinas && $personil->count() > 0) {
            $allPersonilIds = $personil->toArray();
            $hasPltInspektur = false;
            foreach ($allPersonilIds as $pid) {
                $nama = $this->getPegawaiNameFromApi($pid);
                if (stripos($nama, 'BOTTEN TANDIPADA') !== false) {
                    $hasPltInspektur = true;
                    break;
                }
            }

            if ($hasPltInspektur && $personil->count() > 1) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jika Plt. Inspektur dipilih untuk Perjalanan Dinas Luar Daerah, tidak boleh ada pegawai lain yang dipilih!');
            }
        }

        // Check if Ketua Tim or Anggota are already assigned in overlapping dates
        $tanggalMulai = Carbon::parse($validated['tanggal_st']);
        $tanggalSelesai = $tanggalMulai->copy()->addDays($validated['lama_penugasan'] - 1);

        // Check Ketua Tim availability (check both as ketua_tim AND anggota) - only if not Perjalanan Dinas
        if (!$isPerjalananDinas && isset($validated['ketua_tim_id']) && $validated['ketua_tim_id']) {
            $ketuaTimConflict = $this->checkPersonilConflict(
                $validated['ketua_tim_id'],
                $tanggalMulai,
                $tanggalSelesai,
                $id, // exclude current pengawasan
                ['ketua_tim', 'anggota'] // Check both roles
            );

            if ($ketuaTimConflict) {
                $pegawaiName = $this->getPegawaiNameFromApi($validated['ketua_tim_id']);
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Ketua Tim ({$pegawaiName}) sudah bertugas sebagai {$ketuaTimConflict['role']} di rentang tanggal {$ketuaTimConflict['tanggal_mulai']} - {$ketuaTimConflict['tanggal_selesai']} pada pengawasan: {$ketuaTimConflict['nomor_st']}");
            }
        }

        // Check Anggota availability (check both as anggota AND ketua_tim)
        if (isset($validated['anggota']) && count($validated['anggota']) > 0) {
            foreach ($validated['anggota'] as $anggotaId) {
                if (!$anggotaId) continue; // Skip if null or empty

                $anggotaConflict = $this->checkPersonilConflict(
                    $anggotaId,
                    $tanggalMulai,
                    $tanggalSelesai,
                    $id, // exclude current pengawasan
                    ['ketua_tim', 'anggota'] // Check both roles
                );

                if ($anggotaConflict) {
                    $pegawaiName = $this->getPegawaiNameFromApi($anggotaId);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Anggota ({$pegawaiName}) sudah bertugas sebagai {$anggotaConflict['role']} di rentang tanggal {$anggotaConflict['tanggal_mulai']} - {$anggotaConflict['tanggal_selesai']} pada pengawasan: {$anggotaConflict['nomor_st']}");
                }
            }
        }

        try {
            DB::beginTransaction();

            $definitifNama = $pengawasan->penandatangan_definitif_nama ?? \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTEN TANDIPADA';
            $definitifNip = $pengawasan->penandatangan_definitif_nip ?? \App\Models\SystemSetting::where('key', 'definitif_nip')->first()->value ?? '196612141995031001';
            $definitifJabatan = $pengawasan->penandatangan_definitif_jabatan ?? \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. Inspektur';
            $kopPemerintah = $pengawasan->kop_pemerintah ?? \App\Models\SystemSetting::where('key', 'kop_pemerintah')->first()->value ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
            $kopInstansi = $pengawasan->kop_instansi ?? \App\Models\SystemSetting::where('key', 'kop_instansi')->first()->value ?? 'INSPEKTORAT';
            $kopJalan = $pengawasan->kop_jalan ?? \App\Models\SystemSetting::where('key', 'kop_jalan')->first()->value ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
            $kopEmail = $pengawasan->kop_email ?? \App\Models\SystemSetting::where('key', 'kop_email')->first()->value ?? 'Email: inspektorat@puncakjayakab.go.id';

            $pengawasan->update([
                'nomor_st' => $nomorST,
                'tanggal_st' => $validated['tanggal_st'],
                'lama_penugasan' => $validated['lama_penugasan'],
                'uraian_penugasan' => $validated['uraian_penugasan'],
                'lokasi_penugasan' => $validated['lokasi_penugasan'],
                'alat_angkut' => $validated['alat_angkut'],
                'jenis_penugasan' => $validated['jenis_penugasan'],
                'penanggung_jawab_id' => $validated['penanggung_jawab_id'] ?? null,
                'pengendali_teknis_id' => $validated['pengendali_teknis_id'] ?? null,
                'ketua_tim_id' => $validated['ketua_tim_id'] ?? null,
                'penandatangan_type' => $validated['penandatangan_type'],
                'penandatangan_plh_nama' => $validated['penandatangan_type'] === 'plh' ? $validated['penandatangan_plh_nama'] : null,
                'penandatangan_plh_jabatan' => $validated['penandatangan_type'] === 'plh' ? $validated['penandatangan_plh_jabatan'] : null,
                'penandatangan_definitif_nama' => $definitifNama,
                'penandatangan_definitif_nip' => $definitifNip,
                'penandatangan_definitif_jabatan' => $definitifJabatan,
                'kop_pemerintah' => $kopPemerintah,
                'kop_instansi' => $kopInstansi,
                'kop_jalan' => $kopJalan,
                'kop_email' => $kopEmail,
            ]);

            // Update personil anggota
            \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->where('pengawasan_id', $pengawasan->id)->delete();
            if (isset($validated['anggota']) && count($validated['anggota']) > 0) {
                foreach ($validated['anggota'] as $anggotaId) {
                    \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->insert([
                        'pengawasan_id' => $pengawasan->id,
                        'pegawai_id' => $anggotaId
                    ]);
                }
            }

            // Update dasar hukum - delete old and create new
            $pengawasan->dasarHukum()->delete();
            if (isset($validated['dasar_hukum']) && count($validated['dasar_hukum']) > 0) {
                foreach ($validated['dasar_hukum'] as $index => $dasar) {
                    $pengawasan->dasarHukum()->create([
                        'isi_dasar' => $dasar,
                        'urutan' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            $this->createPersonilSnapshot($pengawasan);

            return redirect()->route('pengawasan.index')
                ->with('success', 'Data Pengawasan berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $pengawasan = Pengawasan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:belum_selesai,selesai',
            'file_laporan' => 'nullable|file|mimes:pdf|max:10240', // max 10MB
        ]);

        if ($validated['status'] === 'selesai' && !$pengawasan->file_laporan && !$request->hasFile('file_laporan')) {
            return redirect()->back()
                ->with('error', 'Status tidak dapat diubah menjadi Selesai sebelum mengupload file laporan.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $updateData = ['status' => $validated['status']];

            if ($request->hasFile('file_laporan')) {
                // Delete old file if exists
                if ($pengawasan->file_laporan) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($pengawasan->file_laporan);
                }

                $file = $request->file('file_laporan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('laporan_pengawasan', $filename, 'public');
                $updateData['file_laporan'] = $path;
            }

            \Illuminate\Support\Facades\DB::table('pengawasan')
                ->where('id', $pengawasan->id)
                ->update(array_merge($updateData, ['updated_at' => now()]));

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pengawasan.index')
                ->with('success', 'Status dan laporan berhasil diperbarui!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pengawasan = Pengawasan::findOrFail($id);
            $nomorST = $pengawasan->nomor_st;

            if ($pengawasan->file_laporan) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengawasan->file_laporan);
            }

            // Manual cascade delete because DB_FOREIGN_KEYS=false in SQLite
            \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->where('pengawasan_id', $id)->delete();
            \Illuminate\Support\Facades\DB::table('pengawasan_dasar')->where('pengawasan_id', $id)->delete();

            $pengawasan->delete();

            return redirect()->route('pengawasan.index')
                ->with('success', 'Data Pengawasan ' . $nomorST . ' berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('pengawasan.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get kalender data for dashboard
     */
    public function getKalenderData(Request $request)
    {
        // FullCalendar sends 'start' and 'end' query parameters (ISO8601 strings)
        $start = $request->get('start');
        $end = $request->get('end');

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            
            // Get all pengawasan that overlap with this range
            $pengawasan = Pengawasan::where(function($query) use ($startDate, $endDate) {
                // If the start date is in the range, or end date is in the range
                // Since we only store tanggal_st and lama_penugasan, we can just get all for the year range
                $query->whereYear('tanggal_st', '>=', $startDate->year)
                      ->whereYear('tanggal_st', '<=', $endDate->year);
            })->get();
        } else {
            $bulan = $request->get('bulan', now()->month);
            $tahun = $request->get('tahun', now()->year);
            $pengawasan = Pengawasan::whereYear('tanggal_st', $tahun)->get();
            $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth();
        }

        $this->injectPegawaiData($pengawasan);

        $pengawasan = $pengawasan->map(function ($item) {
                // Calculate end date based on lama_penugasan
                $tanggalMulai = $item->tanggal_st;
                $tanggalSelesai = $tanggalMulai ? $tanggalMulai->copy()->addDays($item->lama_penugasan - 1) : null;
                $anggota = [];
                if (isset($item->anggota)) {
                    foreach($item->anggota as $a) {
                        $anggota[] = $a->nama;
                    }
                }

                return [
                    'id' => $item->id,
                    'nomor_st' => $item->nomor_st,
                    'tanggal_st' => $tanggalMulai ? $tanggalMulai->format('Y-m-d') : null,
                    'jenis' => $item->jenis_penugasan,
                    'uraian' => $item->uraian_penugasan,
                    'lokasi' => $item->lokasi_penugasan,
                    'lama_penugasan' => $item->lama_penugasan,
                    'tanggal_mulai' => $tanggalMulai ? $tanggalMulai->format('Y-m-d') : null,
                    'tanggal_selesai' => $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : null,
                    'penanggung_jawab' => $item->penanggungJawab ? $item->penanggungJawab->nama : null,
                    'pengendali_teknis' => $item->pengendaliTeknis ? $item->pengendaliTeknis->nama : null,
                    'ketua_tim' => $item->ketuaTim ? $item->ketuaTim->nama : null,
                    'anggota' => $anggota,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'file_laporan' => $item->file_laporan,
                    // FullCalendar fields
                    'title' => $item->nomor_st,
                    'start' => $tanggalMulai ? $tanggalMulai->format('Y-m-d') : null,
                    // FullCalendar expects end date to be exclusive for all-day events
                    'end' => $tanggalSelesai ? $tanggalSelesai->copy()->addDay()->format('Y-m-d') : null,
                    'url' => route('pengawasan.show', $item->id),
                    'classNames' => $item->status === 'selesai' ? ['event-selesai'] : ['event-belum-selesai'],
                    'allDay' => true,
                ];
            })
            ->filter(function ($item) use ($startDate, $endDate) {
                // Filter to only include pengawasan that overlap with the specified range
                if (!$item['tanggal_mulai'] || !$item['tanggal_selesai']) {
                    return false;
                }

                $pengawasanStart = \Carbon\Carbon::parse($item['tanggal_mulai']);
                $pengawasanEnd = \Carbon\Carbon::parse($item['tanggal_selesai']);

                // Check if pengawasan overlaps with the range
                return $pengawasanStart->lte($endDate) && $pengawasanEnd->gte($startDate);
            })
            ->values();

        return response()->json($pengawasan);
    }

    /**
     * Generate PDF Surat Tugas
     */
    public function cetakSuratTugas(string $id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])
            ->findOrFail($id);
            
        $this->injectPegawaiData([$pengawasan]);

        $pegawaiPlh = $this->resolvePegawaiPlh($pengawasan);

        // Data untuk PDF
        $data = [
            'pengawasan' => $pengawasan,
            'pegawaiPlh' => $pegawaiPlh,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        // Generate PDF dengan konfigurasi
        $pdf = Pdf::loadView('pengawasan.surat-tugas-pdf', $data);

        // Set paper size A4 portrait
        $pdf->setPaper('A4', 'portrait');

        // Set options untuk DomPDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'dpi' => 96,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
        ]);

        // Stream PDF (preview di tab baru)
        $filename = 'Surat_Tugas_' . str_replace(['/', ' '], '_', $pengawasan->nomor_st) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Check if personil has conflict with existing pengawasan in date range
     *
     * @param int $pegawaiId
     * @param Carbon $tanggalMulai
     * @param Carbon $tanggalSelesai
     * @param int|null $excludePengawasanId - ID pengawasan yang dikecualikan (untuk edit)
     * @param array $roles - Roles to check: ['ketua_tim', 'anggota']
     * @return array|null - Returns conflict info or null if no conflict
     */
    private function checkPersonilConflict($pegawaiId, $tanggalMulai, $tanggalSelesai, $excludePengawasanId = null, $roles = ['ketua_tim', 'anggota'])
    {
        // Query pengawasan yang overlap dengan rentang tanggal
        $query = Pengawasan::query();

        // Exclude current pengawasan if editing
        if ($excludePengawasanId) {
            $query->where('id', '!=', $excludePengawasanId);
        }

        // Get all pengawasan
        $allPengawasan = $query->get();

        foreach ($allPengawasan as $pengawasan) {
            $existingMulai = Carbon::parse($pengawasan->tanggal_st);
            $existingSelesai = $existingMulai->copy()->addDays($pengawasan->lama_penugasan - 1);

            // Check if dates overlap
            $isOverlap = $tanggalMulai->lte($existingSelesai) && $tanggalSelesai->gte($existingMulai);

            if (!$isOverlap) {
                continue;
            }

            // Check if pegawai is assigned in the specified roles
            $assignedRole = null;

            if (in_array('ketua_tim', $roles) && $pengawasan->ketua_tim_id == $pegawaiId) {
                $assignedRole = 'Ketua Tim';
            }

            // 4. Sebagai Anggota
            if (in_array('anggota', $roles) && \Illuminate\Support\Facades\DB::table('pengawasan_anggota')->where('pengawasan_id', $pengawasan->id)->where('pegawai_id', $pegawaiId)->exists()) {
                $assignedRole = 'Anggota';
            }

            if ($assignedRole) {
                return [
                    'nomor_st' => $pengawasan->nomor_st,
                    'tanggal_mulai' => $existingMulai->format('d/m/Y'),
                    'tanggal_selesai' => $existingSelesai->format('d/m/Y'),
                    'jenis_penugasan' => $pengawasan->jenis_penugasan,
                    'role' => $assignedRole,
                ];
            }
        }

        return null;
    }

    /**
     * API: Check personil availability for date range
     */
    public function checkPersonilAvailability(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|integer',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'role' => 'required|in:ketua_tim,anggota',
            'exclude_pengawasan_id' => 'nullable|exists:pengawasan,id',
        ]);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
        $role = $validated['role'];
        $excludeId = $validated['exclude_pengawasan_id'] ?? null;

        $conflict = $this->checkPersonilConflict(
            $validated['pegawai_id'],
            $tanggalMulai,
            $tanggalSelesai,
            $excludeId,
            [$role]
        );

        if ($conflict) {
            return response()->json([
                'available' => false,
                'conflict' => $conflict,
            ]);
        }

        return response()->json([
            'available' => true,
        ]);
    }

    /**
     * Cetak SPPD untuk personil
     */
    public function cetakSppd($id, $pegawai_id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])->findOrFail($id);
        $this->injectPegawaiData([$pengawasan]);
        $pegawai = null;
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) $pegawai = $pengawasan->penanggungJawab;
        elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) $pegawai = $pengawasan->pengendaliTeknis;
        elseif ($pengawasan->ketua_tim_id == $pegawai_id) $pegawai = $pengawasan->ketuaTim;
        else {
            foreach ($pengawasan->anggota as $a) {
                if ($a->id == $pegawai_id) {
                    $pegawai = $a;
                    break;
                }
            }
        }

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Gagal mengambil data pegawai dari API untuk dicetak.');
        }

        // Tentukan role pegawai dalam penugasan
        $role = '';
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) {
            $role = 'Penanggung Jawab';
        } elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) {
            $role = 'Pengendali Teknis';
        } elseif ($pengawasan->ketua_tim_id == $pegawai_id) {
            $role = 'Ketua Tim';
        } else {
            $role = 'Anggota Tim';
        }

        // Clean filename - remove invalid characters (/, \, :, *, ?, ", <, >, |)
        $cleanNomorSt = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $pengawasan->nomor_st);
        $cleanNama = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $pegawai->nama);

        $pegawaiPlh = $this->resolvePegawaiPlh($pengawasan);

        $pdf = PDF::loadView('pengawasan.cetak-sppd', compact('pengawasan', 'pegawai', 'role', 'pegawaiPlh'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('SPPD-' . $cleanNama . '-' . $cleanNomorSt . '.pdf');
    }

    /**
     * Cetak Kwitansi untuk personil
     */
    public function cetakKwitansi(Request $request, $id, $pegawai_id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])->findOrFail($id);
        $this->injectPegawaiData([$pengawasan]);
        $pegawai = null;
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) $pegawai = $pengawasan->penanggungJawab;
        elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) $pegawai = $pengawasan->pengendaliTeknis;
        elseif ($pengawasan->ketua_tim_id == $pegawai_id) $pegawai = $pengawasan->ketuaTim;
        else {
            foreach ($pengawasan->anggota as $a) {
                if ($a->id == $pegawai_id) {
                    $pegawai = $a;
                    break;
                }
            }
        }

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Gagal mengambil data pegawai dari API untuk dicetak.');
        }
        $nominal = $request->input('nominal', 0);

        // Tentukan role pegawai dalam penugasan
        $role = '';
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) {
            $role = 'Penanggung Jawab';
        } elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) {
            $role = 'Pengendali Teknis';
        } elseif ($pengawasan->ketua_tim_id == $pegawai_id) {
            $role = 'Ketua Tim';
        } else {
            $role = 'Anggota Tim';
        }

        // Clean filename - remove invalid characters
        $cleanNomorSt = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $pengawasan->nomor_st);
        $cleanNama = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $pegawai->nama);

        $pegawaiPlh = $this->resolvePegawaiPlh($pengawasan);

        $pdf = PDF::loadView('pengawasan.cetak-kwitansi', compact('pengawasan', 'pegawai', 'role', 'nominal', 'pegawaiPlh'));

        // Set paper to A5 landscape (half of A4)
        $pdf->setPaper('A5', 'landscape');

        return $pdf->stream('Kwitansi-' . $cleanNama . '-' . $cleanNomorSt . '.pdf');
    }

    public function laporan(Request $request)
    {
        $query = Pengawasan::query()
            ->where('status', 'selesai')
            ->whereNotNull('file_laporan');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_st', $request->tahun);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_penugasan', $request->jenis);
        }

        $pengawasan = $query->orderBy('tanggal_st', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        $this->injectPegawaiData($pengawasan->items());

        // Get unique years and types for filters
        $tahunList = Pengawasan::whereNotNull('tanggal_st')
            ->pluck('tanggal_st')
            ->map(function($date) {
                return \Carbon\Carbon::parse($date)->year;
            })
            ->unique()
            ->sortDesc()
            ->values();
            
        $jenisList = Pengawasan::whereNotNull('jenis_penugasan')
            ->select('jenis_penugasan')
            ->distinct()
            ->orderBy('jenis_penugasan')
            ->pluck('jenis_penugasan');

        return view('laporan.index', compact('pengawasan', 'tahunList', 'jenisList'));
    }

    /**
     * Helper to get Pegawai name from API list
     */
    private function getPegawaiNameFromApi($id)
    {
        $response = \Illuminate\Support\Facades\Http::withToken(config('services.pegawai.token'))
            ->get(config('services.pegawai.url'), ['per_page' => 1000]);
        if ($response->successful()) {
            foreach ($response->json()['data'] as $p) {
                if ($p['id'] == $id) return $p['nama'];
            }
        }
        return 'Pegawai';
    }
}
