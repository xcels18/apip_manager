<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Perintah Tugas - {{ $pengawasan->nomor_st }}</title>
    <style type="text/css">
        @page {
            margin: 1cm 2cm 1.5cm 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 8px 15px 10px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
            margin: 0;
        }

        .kop-logo-left {
            width: 60px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo-left img {
            width: 55px;
            height: auto;
            display: block;
            margin: 0 auto;
            vertical-align: middle;

        }

        .kop-text {
            text-align: center;
            padding: 0 5px;
        }

        .kop-logo-right {
            width: 35px;
        }

        .kop-header {
            font-weight: bold;
            font-size: 15pt;
            margin: 0;
            padding: 1px 0;
            line-height: 1.1;
        }

        .kop-subheader {
            font-size: 17pt;
            font-weight: bold;
            margin: 0;
            padding: 1px 0;
            line-height: 1.1;
        }

        .kop-alamat {
            font-size: 8pt;
            margin: 0;
            padding: 1px 0;
            line-height: 1.2;
        }

        .judul {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 6px 0 4px 0;
            padding: 0;
        }

        .nomor {
            text-align: center;
            font-size: 10pt;
            margin: 0 0 4px 0;
            padding: 0;
        }

        .section-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 6px 0 6px 0;
            padding: 0;
        }

        .content-table {
            width: 100%;
            margin: 8px 0;
        }

        .content-table td {
            padding: 2px 0;
            vertical-align: top;
            line-height: 1.3;
        }

        .label {
            width: 70px;
            font-weight: bold;
        }

        .colon {
            width: 12px;
            text-align: center;
        }

        .personil {
            margin: 8px 0 8px 0;
            font-size: 10pt;
            text-align: center;
        }

        .personil-table {
            width: 70%;
            margin: 0 auto;
            border-collapse: collapse;
            text-align: left;
        }

        .personil-table td {
            padding: 3px 5px;
            vertical-align: top;
            line-height: 1.4;
        }

        .personil-table .no {
            width: 30px;
            text-align: left;
        }

        .personil-table .nama {
            width: 250px;
            text-align: left;
        }

        .personil-table .jabatan {
            width: 150px;
            text-align: left;
        }

        /* Style untuk Perjalanan Dinas */
        .personil-detail-wrapper {
            width: 73%;
            margin: 0 auto;
        }

        .personil-detail-table {
            width: 100%;
            margin: 3px 0;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .personil-detail-table td {
            padding: 1px 0;
            vertical-align: top;
            line-height: 1.3;
        }

        .personil-item {
            page-break-inside: avoid;
        }

        .personil-detail-table .detail-no {
            width: 25px;
            text-align: left;
            font-weight: bold;
        }

        .personil-detail-table .detail-label {
            width: 130px;
            text-align: left;
        }

        .personil-detail-table .detail-colon {
            width: 10px;
            text-align: left;
        }

        .personil-detail-table .detail-value {
            text-align: left;
        }

        .personil-separator {
            margin: 4px 0;
        }

        /* Prevent orphaned signature */
        .last-content-item {
            page-break-after: avoid;
        }

        .ttd {
            page-break-inside: avoid;
            page-break-before: avoid;
        }

        .ttd-content {
            page-break-inside: avoid;
        }

        .keep-with-signature {
            page-break-inside: avoid;
        }

        ol {
            margin: 0;
            padding-left: 18px;
        }

        ol li {
            margin: 3px 0;
            text-align: justify;
            line-height: 1.4;
        }

        ol li:first-child {
            margin-top: 0;
        }

        ol li:last-child {
            margin-bottom: 0;
        }

        .keep-with-signature ol {
            margin-top: 0;
        }

        .keep-with-signature .content-table {
            margin-top: 0;
        }

        .sub-detail {
            margin: 3px 0;
            font-size: 9pt;
        }

        .ttd {
            margin-top: 8px;
            text-align: right;
        }

        .ttd-content {
            display: inline-block;
            text-align: left;
            min-width: 220px;
            font-size: 9pt;
        }

        .ttd-tempat {
            text-align: left;
            margin-bottom: 2px;
        }

        .ttd-jabatan {
            font-weight: bold;
            margin: 2px 0;
        }

        .ttd-space {
            margin: 40px 0;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 2px 0;
        }

        .ttd-detail {
            margin: 1px 0;
        }
    </style>
</head>
<body>
    @php
        $isPlh = ($pengawasan->penandatangan_type ?? 'definitif') === 'plh';
        // Logika lama: Perjalanan Dinas Inspektur (kop surat berbeda)
        $isPerjalananDinasInspektur = false;
        $allPersonil = collect();

        if($pengawasan->penanggungJawab) {
            $allPersonil->push($pengawasan->penanggungJawab);
        }
        if($pengawasan->pengendaliTeknis) {
            $allPersonil->push($pengawasan->pengendaliTeknis);
        }
        if($pengawasan->ketuaTim) {
            $allPersonil->push($pengawasan->ketuaTim);
        }
        foreach($pengawasan->anggota as $anggota) {
            $allPersonil->push($anggota);
        }

        if ($pengawasan->jenis_penugasan === 'Perjalanan Dinas Luar Daerah') {
            foreach ($allPersonil as $person) {
                if (stripos($person->nama ?? '', 'BOTTEN TANDIPADA') !== false) {
                    $isPerjalananDinasInspektur = true;
                    break;
                }
            }
        }
    @endphp

    <!-- KOP SURAT -->
    @php
        $kopPemerintah = $pengawasan->kop_pemerintah ?? \App\Models\SystemSetting::where('key', 'kop_pemerintah')->first()->value ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
        $kopInstansi = $pengawasan->kop_instansi ?? \App\Models\SystemSetting::where('key', 'kop_instansi')->first()->value ?? 'INSPEKTORAT';
        $kopJalan = $pengawasan->kop_jalan ?? \App\Models\SystemSetting::where('key', 'kop_jalan')->first()->value ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
        $kopEmail = $pengawasan->kop_email ?? \App\Models\SystemSetting::where('key', 'kop_email')->first()->value ?? 'Email: inspektorat@puncakjayakab.go.id';
    @endphp
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-left">
                    @php
                        $path = public_path('images/logo_puja.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        if(file_exists($path)) {
                            $data = file_get_contents($path);
                            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        } else {
                            $logoBase64 = null;
                        }
                    @endphp
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Puncak Jaya">
                    @else
                        <div style="width: 55px; height: 55px; border: 2px solid #000; text-align: center; padding: 5px; font-size: 6pt; font-weight: bold;">
                            LOGO<br>PUNCAK<br>JAYA
                        </div>
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-header">{{ $kopPemerintah }}</div>
                    @if($isPerjalananDinasInspektur)
                        <div class="kop-subheader">SEKRETARIAT DAERAH</div>
                        <div class="kop-alamat">Jl. Drs.P.A.Coem No.01 Mulia, Puncak Jaya</div>
                        
                    @else
                        <div class="kop-subheader">{{ $kopInstansi }}</div>
                        <div class="kop-alamat">{{ $kopJalan }}</div>
                        <div class="kop-alamat">{{ $kopEmail }}</div>
                    @endif
                </td>
                <td class="kop-logo-right">
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL -->
    <div class="judul">SURAT PERINTAH TUGAS</div>

    <!-- NOMOR -->
    @php
        $nomorST = $pengawasan->nomor_st;
        if ($isPerjalananDinasInspektur) {
            $nomorST = '100.3.5.4/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/SET';
        }
    @endphp
    @if(!empty($nomorST))
        <div class="nomor">Nomor: {!! $nomorST !!}</div>
        <br>
    @endif

    <!-- DASAR -->
    <table class="content-table">
        <tr>
            <td class="label">Dasar</td>
            <td class="colon">:</td>
            <td>
                @if($pengawasan->dasarHukum->count() > 1)
                    <ol style="margin: 0; padding-left: 20px;">
                        @foreach($pengawasan->dasarHukum as $dasar)
                        <li>{{ $dasar->isi_dasar }}</li>
                        @endforeach
                    </ol>
                @else
                    {{ $pengawasan->dasarHukum->first()->isi_dasar ?? 'Tidak ada dasar hukum' }}
                @endif
            </td>
        </tr>
    </table>

    <!-- MEMERINTAHKAN -->
    <div class="section-title">MEMERINTAHKAN</div>

    <!-- PERSONIL -->
    @php
        $isPerjalananDinas = $pengawasan->jenis_penugasan === 'Perjalanan Dinas Luar Daerah';
    @endphp

    @if($isPerjalananDinas)
        <!-- Format untuk Perjalanan Dinas -->
        <div class="personil-detail-wrapper">
            @foreach($allPersonil as $index => $personil)
                <div class="personil-item">
                    @if($index > 0)
                        <div class="personil-separator"></div>
                    @endif
                    <table class="personil-detail-table">
                        <tr>
                            <td class="detail-no" rowspan="4">{{ $index + 1 }}.</td>
                            <td class="detail-label">Nama</td>
                            <td class="detail-colon">:</td>
                            <td class="detail-value">{{ $personil->nama }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">NIP</td>
                            <td class="detail-colon">:</td>
                            <td class="detail-value">{{ $personil->nip }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Pangkat/Golongan</td>
                            <td class="detail-colon">:</td>
                            <td class="detail-value">{{ $personil->golongan }}</td>
                        </tr>
                        <tr>
                            <td class="detail-label">Jabatan</td>
                            <td class="detail-colon">:</td>
                            <td class="detail-value">{{ $personil->jabatan }}</td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    @else
        <!-- Format untuk jenis penugasan lainnya -->
        <div class="personil">
            <table class="personil-table">
                @php $no = 1; @endphp
                @if($pengawasan->penanggungJawab)
                <tr>
                    <td class="no">{{ $no++ }}.</td>
                    <td class="nama">{{ $pengawasan->penanggungJawab->nama }}</td>
                    <td class="jabatan">(Penanggung Jawab)</td>
                </tr>
                @endif
                @if($pengawasan->pengendaliTeknis)
                <tr>
                    <td class="no">{{ $no++ }}.</td>
                    <td class="nama">{{ $pengawasan->pengendaliTeknis->nama }}</td>
                    <td class="jabatan">(Pengendali Teknis)</td>
                </tr>
                @endif
                @if($pengawasan->ketuaTim)
                <tr>
                    <td class="no">{{ $no++ }}.</td>
                    <td class="nama">{{ $pengawasan->ketuaTim->nama }}</td>
                    <td class="jabatan">(Ketua Tim)</td>
                </tr>
                @endif
                @foreach($pengawasan->anggota as $anggota)
                <tr>
                    <td class="no">{{ $no++ }}.</td>
                    <td class="nama">{{ $anggota->nama }}</td>
                    <td class="jabatan">(Anggota)</td>
                </tr>
                @endforeach
            </table>
        </div>
    @endif

    <!-- UNTUK -->
    @php
        $tanggalBerangkat = \Carbon\Carbon::parse($pengawasan->tanggal_st);
        $tanggalKembali = $tanggalBerangkat->copy()->addDays($pengawasan->lama_penugasan - 1);
    @endphp
    <table class="content-table" style="margin-bottom: 0;">
        <tr>
            <td class="label">UNTUK</td>
            <td class="colon">:</td>
            <td>
                <ol>
                    <li>Setelah menerima surat perintah ini segera berangkat ke {{ $pengawasan->lokasi_penugasan }}.</li>
                    <li>Dalam rangka {{ $pengawasan->uraian_penugasan }}.</li>
                </ol>
            </td>
        </tr>
    </table>

    <!-- Bagian yang harus tetap bersama dengan tanda tangan -->
    <div class="keep-with-signature">
        <table class="content-table">
            <tr>
                <td class="label"></td>
                <td class="colon"></td>
                <td>
                    <ol start="3">
                        <li>Beban biaya Inspektorat Daerah Kabupaten Puncak Jaya Tahun Anggaran 2026.</li>
                        <li>Demikian surat tugas ini dibuat untuk dilaksanakan dengan rasa tanggung jawab.</li>
                    </ol>
                </td>
            </tr>
        </table>

        <!-- TANDA TANGAN -->
        <div class="ttd">
            <div class="ttd-content">
                <div class="ttd-tempat">Dikeluarkan di Mulia</div>
                <div class="ttd-tempat">Pada Tanggal: {{ \Carbon\Carbon::parse($pengawasan->tanggal_st)->locale('id')->translatedFormat('d F Y') }}</div>
                @if($isPerjalananDinasInspektur && !$isPlh)
                    <div class="ttd-jabatan">Pj. Sekretaris Daerah</div>
                    <div class="ttd-jabatan">Kabupaten Puncak Jaya,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">Yubelina Enumbi, SE., MM., MH.</div>
                    <div class="ttd-detail">Pembina Utama Muda</div>
                    <div class="ttd-detail">NIP. 198111182004122001</div>
                @elseif($isPerjalananDinasInspektur && $isPlh)
                    <div class="ttd-jabatan">Plh. Sekretaris Daerah</div>
                    <div class="ttd-jabatan">Kabupaten Puncak Jaya,</div>
                    <div class="ttd-space"></div>
                    <div class="signature-name">{{ $pengawasan->penandatangan_plh_nama }}</div>
                    @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                    <div class="signature-nip">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                    @endif
                    @if($pegawaiPlh && isset($pegawaiPlh->nip))
                    <div class="signature-nip">NIP. {{ $pegawaiPlh->nip }}</div>
                    @endif
                @elseif($isPlh)
                    <div class="ttd-jabatan">Plh. INSPEKTUR,</div>
                    <div class="ttd-space"></div>
                    <div class="signature-name">{{ strtoupper($pengawasan->penandatangan_plh_nama) }}</div>
                    @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                    <div class="signature-nip">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                    @endif
                    @if($pegawaiPlh && isset($pegawaiPlh->nip))
                    <div class="signature-nip">NIP. {{ $pegawaiPlh->nip }}</div>
                    @endif
                @else
                    @php
                        $definitifNama = $pengawasan->penandatangan_definitif_nama ?? \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTENTANDIPADA, ST., M.AP.';
                        $definitifNip = $pengawasan->penandatangan_definitif_nip ?? \App\Models\SystemSetting::where('key', 'definitif_nip')->first()->value ?? '197005102000101006';
                        $definitifJabatan = $pengawasan->penandatangan_definitif_jabatan ?? \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. INSPEKTUR';
                    @endphp
                    <div class="ttd-jabatan">{{ strtoupper($definitifJabatan) }},</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ strtoupper($definitifNama) }}</div>
                    @if($definitifNip)
                    <div class="ttd-detail">NIP. {{ $definitifNip }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</body>
</html>

