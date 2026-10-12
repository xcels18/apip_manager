<?php

namespace App\Http\Controllers;

use App\Models\Pengawasan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasanController extends Controller
{
    public function injectPegawaiData($pengawasanList)
    {
        app(\App\Services\PersonilService::class)->inject($pengawasanList);
    }

    private function createPersonilSnapshot($pengawasan)
    {
        app(\App\Services\PersonilService::class)->snapshot($pengawasan);
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
            $response = app(\App\Services\PegawaiService::class)->get([
                'search' => $pengawasan->penandatangan_plh_nama,
                'per_page' => 1,
            ]);
            if ($response->successful() && ! empty($response->json()['data'])) {
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
            $query->where(function ($q) use ($search) {
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
        $response = app(\App\Services\PegawaiService::class)->allResponse();
        $pegawai = $response->successful() ? collect($response->json()['data'])->map(fn ($p) => (object) $p) : collect();
        if (! $response->successful()) {
            session()->flash('error', 'Layanan pegawai sementara tidak tersedia. Coba kembali sebelum menyimpan penugasan.');
        }
        $tahun = date('Y');

        $formatNomorSt = \App\Models\SystemSetting::valueFor('format_nomor_st') ?? '100.3.5.4/[NOMOR]/ST/ITKAB/[TAHUN]';
        $formatParts = explode('[NOMOR]', $formatNomorSt);
        $stPrefix = $formatParts[0] ?? '100.3.5.4/';
        $stSuffix = $formatParts[1] ?? '/ST/ITKAB/[TAHUN]';
        $stSuffix = str_replace('[TAHUN]', $tahun, $stSuffix);

        return view('pengawasan.create', compact('pegawai', 'tahun', 'stPrefix', 'stSuffix'));
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
            'lama_penugasan' => 'required|integer|min:1|max:366',
            'uraian_penugasan' => 'required|string',
            'lokasi_penugasan' => 'required|string|max:200',
            'alat_angkut' => 'required|in:darat,udara',
            'jenis_penugasan' => 'required|string|max:100',
            'dasar_hukum' => 'required|array|min:1',
            'dasar_hukum.*' => 'required|string',
            'penanggung_jawab_id' => $isPerjalananDinas ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'pengendali_teknis_id' => 'nullable|integer|min:1',
            'ketua_tim_id' => $isPerjalananDinas ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'anggota' => 'nullable|array',
            'anggota.*' => 'required|integer|min:1|distinct',
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
        if (! empty($validated['nomor_st_number'])) {
            $formatNomorSt = \App\Models\SystemSetting::valueFor('format_nomor_st') ?? '100.3.5.4/[NOMOR]/ST/ITKAB/[TAHUN]';
            $formatParts = explode('[NOMOR]', $formatNomorSt);
            $stPrefix = $formatParts[0] ?? '100.3.5.4/';
            $stSuffixRaw = $formatParts[1] ?? '/ST/ITKAB/[TAHUN]';
            $stSuffix = str_replace('[TAHUN]', $tahun, $stSuffixRaw);
            $nomorST = $stPrefix.$validated['nomor_st_number'].$stSuffix;
        }

        \Illuminate\Support\Facades\Validator::make(['nomor_st' => $nomorST], [
            'nomor_st' => ['nullable', 'string', 'max:100', \Illuminate\Validation\Rule::unique('pengawasan')->ignore($pengawasan->id ?? null)],
        ])->validate();

        app(\App\Services\PenugasanService::class)->validate($validated);

        try {
            DB::beginTransaction();

            $definitifNama = \App\Models\SystemSetting::valueFor('definitif_nama') ?? 'BOTTEN TANDIPADA';
            $definitifNip = \App\Models\SystemSetting::valueFor('definitif_nip') ?? '196612141995031001';
            $definitifJabatan = \App\Models\SystemSetting::valueFor('definitif_jabatan') ?? 'Plt. Inspektur';
            $kopPemerintah = \App\Models\SystemSetting::valueFor('kop_pemerintah') ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
            $kopInstansi = \App\Models\SystemSetting::valueFor('kop_instansi') ?? 'INSPEKTORAT';
            $kopJalan = \App\Models\SystemSetting::valueFor('kop_jalan') ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
            $kopEmail = \App\Models\SystemSetting::valueFor('kop_email') ?? 'Email: inspektorat@puncakjayakab.go.id';

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
                        'pegawai_id' => $anggotaId,
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

            $this->createPersonilSnapshot($pengawasan);

            DB::commit();

            return redirect()->route('pengawasan.index')
                ->with('success', 'Data Pengawasan berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \App\Exceptions\PegawaiUnavailable) {
                throw $e;
            }
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Data belum berhasil disimpan. Periksa data personil dan koneksi layanan pegawai, lalu coba kembali.');
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

        $response = app(\App\Services\PegawaiService::class)->allResponse();
        $pegawai = $response->successful() ? collect($response->json()['data'])->map(fn ($p) => (object) $p) : collect();
        if (! $response->successful()) {
            session()->flash('error', 'Layanan pegawai sementara tidak tersedia. Coba kembali sebelum menyimpan penugasan.');
        }
        $tahun = date('Y', strtotime($pengawasan->tanggal_st));

        $formatNomorSt = \App\Models\SystemSetting::valueFor('format_nomor_st') ?? '100.3.5.4/[NOMOR]/ST/ITKAB/[TAHUN]';
        $formatParts = explode('[NOMOR]', $formatNomorSt);
        $stPrefix = $formatParts[0] ?? '100.3.5.4/';
        $stSuffixRaw = $formatParts[1] ?? '/ST/ITKAB/[TAHUN]';
        $stSuffix = str_replace('[TAHUN]', $tahun, $stSuffixRaw);

        // Extract raw number by removing prefix and suffix
        $nomorStNumber = str_replace([$stPrefix, $stSuffix], '', $pengawasan->nomor_st);

        return view('pengawasan.edit', compact('pengawasan', 'pegawai', 'tahun', 'nomorStNumber', 'stPrefix', 'stSuffix'));
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
            'lama_penugasan' => 'required|integer|min:1|max:366',
            'uraian_penugasan' => 'required|string',
            'lokasi_penugasan' => 'required|string|max:200',
            'alat_angkut' => 'required|in:darat,udara',
            'jenis_penugasan' => 'required|string|max:100',
            'dasar_hukum' => 'required|array|min:1',
            'dasar_hukum.*' => 'required|string',
            'penanggung_jawab_id' => $isPerjalananDinas ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'pengendali_teknis_id' => 'nullable|integer|min:1',
            'ketua_tim_id' => $isPerjalananDinas ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'anggota' => 'nullable|array',
            'anggota.*' => 'required|integer|min:1|distinct',
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
        if (! empty($validated['nomor_st_number'])) {
            $formatNomorSt = \App\Models\SystemSetting::valueFor('format_nomor_st') ?? '100.3.5.4/[NOMOR]/ST/ITKAB/[TAHUN]';
            $formatParts = explode('[NOMOR]', $formatNomorSt);
            $stPrefix = $formatParts[0] ?? '100.3.5.4/';
            $stSuffixRaw = $formatParts[1] ?? '/ST/ITKAB/[TAHUN]';
            $stSuffix = str_replace('[TAHUN]', $tahun, $stSuffixRaw);
            $nomorST = $stPrefix.$validated['nomor_st_number'].$stSuffix;
        }

        \Illuminate\Support\Facades\Validator::make(['nomor_st' => $nomorST], [
            'nomor_st' => ['nullable', 'string', 'max:100', \Illuminate\Validation\Rule::unique('pengawasan')->ignore($pengawasan->id ?? null)],
        ])->validate();

        app(\App\Services\PenugasanService::class)->validate($validated, $pengawasan);

        try {
            DB::beginTransaction();

            $definitifNama = $pengawasan->penandatangan_definitif_nama ?? \App\Models\SystemSetting::valueFor('definitif_nama') ?? 'BOTTEN TANDIPADA';
            $definitifNip = $pengawasan->penandatangan_definitif_nip ?? \App\Models\SystemSetting::valueFor('definitif_nip') ?? '196612141995031001';
            $definitifJabatan = $pengawasan->penandatangan_definitif_jabatan ?? \App\Models\SystemSetting::valueFor('definitif_jabatan') ?? 'Plt. Inspektur';
            $kopPemerintah = $pengawasan->kop_pemerintah ?? \App\Models\SystemSetting::valueFor('kop_pemerintah') ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
            $kopInstansi = $pengawasan->kop_instansi ?? \App\Models\SystemSetting::valueFor('kop_instansi') ?? 'INSPEKTORAT';
            $kopJalan = $pengawasan->kop_jalan ?? \App\Models\SystemSetting::valueFor('kop_jalan') ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
            $kopEmail = $pengawasan->kop_email ?? \App\Models\SystemSetting::valueFor('kop_email') ?? 'Email: inspektorat@puncakjayakab.go.id';

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
                        'pegawai_id' => $anggotaId,
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

            $this->createPersonilSnapshot($pengawasan);

            DB::commit();

            return redirect()->route('pengawasan.index')
                ->with('success', 'Data Pengawasan berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \App\Exceptions\PegawaiUnavailable) {
                throw $e;
            }
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Data belum berhasil disimpan. Periksa data personil dan koneksi layanan pegawai, lalu coba kembali.');
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $pengawasan = Pengawasan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:belum_selesai,selesai',
            'file_laporan' => 'nullable|file|mimes:pdf|max:10240', // max 10MB
        ]);

        if ($validated['status'] === 'selesai' && ! $pengawasan->file_laporan && ! $request->hasFile('file_laporan')) {
            return redirect()->back()
                ->with('error', 'Status tidak dapat diubah menjadi Selesai sebelum mengupload file laporan.');
        }

        $newPath = null;
        $oldPath = $pengawasan->file_laporan;
        try {
            if ($request->hasFile('file_laporan')) {
                $newPath = $request->file('file_laporan')->store('laporan_pengawasan', 'public');
                if (! $newPath) {
                    throw new \RuntimeException('Unable to store report');
                }
            }
            DB::transaction(function () use ($pengawasan, $validated, $newPath) {
                $pengawasan->update(['status' => $validated['status'], ...($newPath ? ['file_laporan' => $newPath] : [])]);
            });
        } catch (\Throwable $e) {
            if ($newPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($newPath);
            }
            report($e);

            return back()->with('error', 'Laporan gagal disimpan. Laporan sebelumnya tetap tersedia.');
        }
        if ($newPath && $oldPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('pengawasan.index')->with('success', 'Status dan laporan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengawasan = Pengawasan::findOrFail($id);
        try {
            DB::transaction(function () use ($pengawasan) {
                DB::table('pengawasan_anggota')->where('pengawasan_id', $pengawasan->id)->delete();
                $pengawasan->dasarHukum()->delete();
                $pengawasan->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('pengawasan.index')->with('error', 'Pengawasan gagal dihapus. Silakan coba kembali.');
        }
        if ($pengawasan->file_laporan) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengawasan->file_laporan);
        }

        return redirect()->route('pengawasan.index')->with('success', 'Data Pengawasan berhasil dihapus!');
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
            $pengawasan = Pengawasan::where(function ($query) use ($startDate, $endDate) {
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
                foreach ($item->anggota as $a) {
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
                if (! $item['tanggal_mulai'] || ! $item['tanggal_selesai']) {
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
        $filename = 'Surat_Tugas_'.str_replace(['/', ' '], '_', $pengawasan->nomor_st).'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Check if personil has conflict with existing pengawasan in date range
     *
     * @param  int  $pegawaiId
     * @param  Carbon  $tanggalMulai
     * @param  Carbon  $tanggalSelesai
     * @param  int|null  $excludePengawasanId  - ID pengawasan yang dikecualikan (untuk edit)
     * @param  array  $roles  - Roles to check: ['ketua_tim', 'anggota']
     * @return array|null - Returns conflict info or null if no conflict
     */
    private function checkPersonilConflict($pegawaiId, $tanggalMulai, $tanggalSelesai, $excludePengawasanId = null, $roles = ['ketua_tim', 'anggota'])
    {
        return app(\App\Services\PenugasanService::class)->conflict($pegawaiId, $tanggalMulai, $tanggalSelesai, $excludePengawasanId, $roles);
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
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) {
            $pegawai = $pengawasan->penanggungJawab;
        } elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) {
            $pegawai = $pengawasan->pengendaliTeknis;
        } elseif ($pengawasan->ketua_tim_id == $pegawai_id) {
            $pegawai = $pengawasan->ketuaTim;
        } else {
            foreach ($pengawasan->anggota as $a) {
                if ($a->id == $pegawai_id) {
                    $pegawai = $a;
                    break;
                }
            }
        }

        if (! $pegawai) {
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

        return $pdf->stream('SPPD-'.$cleanNama.'-'.$cleanNomorSt.'.pdf');
    }

    /**
     * Cetak Kwitansi untuk personil
     */
    public function cetakKwitansi(Request $request, $id, $pegawai_id)
    {
        $pengawasan = Pengawasan::with(['dasarHukum'])->findOrFail($id);
        $this->injectPegawaiData([$pengawasan]);
        $pegawai = null;
        if ($pengawasan->penanggung_jawab_id == $pegawai_id) {
            $pegawai = $pengawasan->penanggungJawab;
        } elseif ($pengawasan->pengendali_teknis_id == $pegawai_id) {
            $pegawai = $pengawasan->pengendaliTeknis;
        } elseif ($pengawasan->ketua_tim_id == $pegawai_id) {
            $pegawai = $pengawasan->ketuaTim;
        } else {
            foreach ($pengawasan->anggota as $a) {
                if ($a->id == $pegawai_id) {
                    $pegawai = $a;
                    break;
                }
            }
        }

        if (! $pegawai) {
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

        return $pdf->stream('Kwitansi-'.$cleanNama.'-'.$cleanNomorSt.'.pdf');
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
            ->map(function ($date) {
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
}
