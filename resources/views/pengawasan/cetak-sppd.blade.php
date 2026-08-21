@php
    use Carbon\Carbon;
    $definitifNama = \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTENTANDIPADA, ST., M.AP.';
    $definitifNip = \App\Models\SystemSetting::where('key', 'definitif_nip')->first()->value ?? '197005102000101006';
    $definitifJabatan = \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. INSPEKTUR';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SPPD - {{ $pegawai->nama }}</title>
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
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: top;
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
        }

        .kop-text {
            text-align: center;
            padding: 0 5px;
        }

        .kop-header {
            font-weight: bold;
            font-size: 14pt;
            margin: 0;
            padding: 1px 0;
            line-height: 1.1;
        }

        .kop-subheader {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            padding: 0px 0;
            line-height: 1.1;
        }

        .kop-alamat {
            font-size: 8pt;
            margin: 0;
            padding: 1px 0;
            line-height: 1.2;
        }

        .info-box {
            margin: 10px 0;
            font-size: 9pt;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 0;
            margin: 0;
        }

        .info-table .info-left {
            width: 55%;
        }

        .info-table .info-right {
            width: 45%;
            text-align: left;
        }

        .info-row {
            margin: 2px 0;
            line-height: 1.3;
        }

        .info-label {
            display: inline-block;
            width: 70px;
        }

        .info-colon {
            display: inline-block;
            width: 10px;
        }

        .info-value {
            display: inline-block;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 8px 0 3px 0;
            text-decoration: underline;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 10pt;
            margin: 0 0 12px 0;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
        }

        .content-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
            line-height: 1.3;
        }

        .content-table .no-col {
            width: 25px;
            text-align: center;
            font-weight: bold;
        }

        .content-table .label-col {
            width: 38%;
        }

        .content-table .value-col {
            width: auto;
        }

        .footer-section {
            margin-top: 15px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            padding: 0;
            vertical-align: top;
        }

        .footer-left {
            width: 50%;
            font-size: 9pt;
        }

        .footer-right {
            width: 50%;
            text-align: left;
            font-size: 9pt;
        }

        .signature-box {
            margin-top: 5px;
            text-align: left;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 50px;
            text-align: left;
        }

        .signature-nip {
            font-size: 9pt;
            margin-top: 2px;
        }

        /* Halaman 2 Styles */
        .page-break {
            page-break-before: always;
        }

        .page2-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 9pt;
        }

        .page2-table td {
            border: 1px solid #000;
            padding: 8px 5px;
            vertical-align: top;
            line-height: 1;
        }

        .page2-header {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px 5px;
            font-size: 9pt;
            line-height: 1.3;
        }

        .page2-col-left {
            width: 50%;
        }

        .page2-col-right {
            width: 50%;
        }

        .page2-row-label {
            font-weight: regular;
            font-size: 8pt;
        }

        .page2-dotted {
            border-bottom: 1px dotted #000;
            min-height: 15px;
            margin: 3px 0;
        }

        .page2-footer {
            margin-top: 5px;
            font-size: 8pt;
            border: 1px solid #000;
            padding: 8px;
            line-height: 1.4;
        }

        .page2-footer-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .page2-field {
            margin: 3px 0;
            line-height: 1.1;
        }

        .page2-field-label {
            display: inline-block;
            width: 95px;
            font-weight: regular;
            font-size: 8pt;
        }

        .page2-field-colon {
            display: inline-block;
            width: 12px;
            font-size: 8pt;
        }

        .page2-field-value {
            display: inline-block;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    @php
        $isPlh = ($pengawasan->penandatangan_type ?? 'definitif') === 'plh';

        // Logika lama: Perjalanan Dinas Inspektur (kop surat berbeda)
        $isPerjalananDinasInspektur = false;

        if ($pengawasan->jenis_penugasan === 'Perjalanan Dinas Luar Daerah') {
            // Check if the current pegawai is Plt. Inspektur (BOTTEN TANDIPADA)
            if (stripos($pegawai->nama ?? '', 'BOTTEN TANDIPADA') !== false) {
                $isPerjalananDinasInspektur = true;
            }
        }
    @endphp

    @php
        $kopPemerintah = \App\Models\SystemSetting::where('key', 'kop_pemerintah')->first()->value ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
        $kopInstansi = \App\Models\SystemSetting::where('key', 'kop_instansi')->first()->value ?? 'INSPEKTORAT';
        $kopJalan = \App\Models\SystemSetting::where('key', 'kop_jalan')->first()->value ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
        $kopEmail = \App\Models\SystemSetting::where('key', 'kop_email')->first()->value ?? 'Email: inspektorat@puncakjayakab.go.id';
    @endphp

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-left">
                    @if(file_exists(public_path('images/logo_puja.png')))
                        <img src="{{ public_path('images/logo_puja.png') }}" alt="Logo Puncak Jaya">
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
                <td class="kop-logo-right"></td>
            </tr>
        </table>
    </div>

    <!-- INFO BOX -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-left"></td>
                <td class="info-right">
                    <div class="info-row">
                        <span class="info-label">Lembar</span>
                        <span class="info-colon">:</span>
                        <span class="info-value">I, II, III, IV</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kode No</span>
                        <span class="info-colon">:</span>
                        <span class="info-value">-</span>
                    </div>
                     @if($isPerjalananDinasInspektur)
                    <div class="info-row">
                        <span class="info-label">Nomor</span>
                        <span class="info-colon">:</span>
                        <span class="info-value">000.1.3.3/<span style="display: inline-block; width: 20px; border-bottom: 1px solid #000; margin: 0 2px;"></span>/SPPD/SET/{{ \Carbon\Carbon::parse($pengawasan->created_at)->format('Y') }}</span>
                    </div>
                    @else
                    <div class="info-row">
                        <span class="info-label">Nomor</span>
                        <span class="info-colon">:</span>
                        <span class="info-value">000.1.3.3/<span style="display: inline-block; width: 20px; border-bottom: 1px solid #000; margin: 0 2px;"></span>/SPPD/ITKAB/{{ \Carbon\Carbon::parse($pengawasan->created_at)->format('Y') }}</span>
                    </div>
                    @endif

                </td>
            </tr>
        </table>
    </div>

    <!-- TITLE -->
    <div class="doc-title">SURAT PERINTAH PERJALANAN DINAS</div>
    <div class="doc-subtitle">(SPPD)</div>

    <!-- CONTENT TABLE -->
    <table class="content-table">
        <tr>
            <td class="no-col">1.</td>
            <td class="label-col">Pejabat yang memberi Perintah</td>
            <td class="value-col">
                @if($isPerjalananDinasInspektur && !$isPlh)
                    Pj. Sekretaris Daerah Kabupaten Puncak Jaya
                @elseif($isPerjalananDinasInspektur && $isPlh)
                    Plh. Sekretaris Daerah Kabupaten Puncak Jaya
                @elseif($isPlh)
                    Plh. Inspektur Kabupaten Puncak Jaya
                @else
                    Plt. Inspektur Kabupaten Puncak Jaya
                @endif
            </td>
        </tr>
        <tr>
            <td class="no-col">2.</td>
            <td class="label-col">Nama Pegawai yang Melaksanakan Perjalanan Dinas</td>
            <td class="value-col">
                {{ $pegawai->nama ?? '-' }}<br>
                NIP. {{ $pegawai->nip ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="no-col">3.</td>
            <td class="label-col">
                a. Pangkat / Golongan<br>
                b. Jabatan<br>
                c. Tingkat Menurut Peraturan Perjalanan
            </td>
            <td class="value-col">
                {{ $pegawai->golongan ?? '-' }}<br>
                {{ $pegawai->jabatan ?? '-' }}<br>
               
            </td>
        </tr>
        <tr>
            <td class="no-col">4.</td>
            <td class="label-col">Maksud Perjalanan Dinas</td>
            <td class="value-col">{{ $pengawasan->uraian_penugasan }}</td>
        </tr>
        <tr>
            <td class="no-col">5.</td>
            <td class="label-col">Alat angkut yang dipergunakan</td>
            <td class="value-col">{{ $pengawasan->alat_angkut_label }}</td>
        </tr>
        <tr>
            <td class="no-col">6.</td>
            <td class="label-col">
                a. Tempat Berangkat<br>
                b. Tempat Tujuan
            </td>
            <td class="value-col">
                Mulia<br>
                {{ $pengawasan->lokasi_penugasan }}
            </td>
        </tr>
        <tr>
            <td class="no-col">7.</td>
            <td class="label-col">
                a. Lamanya Perjalanan Dinas<br>
                b. Tanggal Berangkat<br>
                c. Tanggal harus kembali
            </td>
            <td class="value-col">
                {{ $pengawasan->lama_penugasan }} Hari <br>
    
            </td>
        </tr>
        <tr>
            <td class="no-col">8.</td>
            <td class="label-col">Pengikut</td>
            <td class="value-col">
                1.<br>
                2.<br>
                3.<br>
                4.
            </td>
        </tr>
        <tr>
            <td class="no-col">9.</td>
            <td class="label-col">
                Pembebanan Anggaran Pada :<br>
                a. Instansi<br>
                b. Kode Rekening<br>
                c. DPA
            </td>
            <td class="value-col">
                <br>
                Inspektorat Daerah Kab. Puncak jaya<br>
                <br>
                Tahun {{ \Carbon\Carbon::parse($pengawasan->created_at)->format('Y') }}
            </td>
        </tr>
        <tr>
            <td class="no-col">10.</td>
            <td class="label-col">Keterangan lain-lain</td>
            <td class="value-col">-</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer-section">
        <table class="footer-table">
            <tr>
                <td class="footer-left"></td>
                <td class="footer-right">
                    <div>Dikeluarkan di: Mulia</div>
                    <div>Pada Tanggal: {{ \Carbon\Carbon::parse($pengawasan->tanggal_st)->locale('id')->translatedFormat('d F Y') }}</div>
                    <br>
                    <div class="signature-box">
                        @if($isPerjalananDinasInspektur && !$isPlh)
                            <div>Pj. Sekretaris Daerah</div>
                            <div>Kabupaten Puncak Jaya,</div>
                            <div class="signature-name">Yubelina Enumbi, SE., MM., MH.</div>
                            <div class="signature-nip">Pembina Utama Muda</div>
                            <div class="signature-nip">NIP. 198111182004122001</div>
                        @elseif($isPerjalananDinasInspektur && $isPlh)
                            <div>Plh. Sekretaris Daerah</div>
                            <div>Kabupaten Puncak Jaya,</div>
                            <div class="signature-name">{{ $pengawasan->penandatangan_plh_nama }}</div>
                            @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                            <div class="signature-nip">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                            @endif
                            @if($pegawaiPlh && isset($pegawaiPlh->nip))
                            <div class="signature-nip">NIP. {{ $pegawaiPlh->nip }}</div>
                            @endif
                        @elseif($isPlh)
                            <div>Plh. INSPEKTUR,</div>
                            <div class="signature-name">{{ strtoupper($pengawasan->penandatangan_plh_nama) }}</div>
                            @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                            <div class="signature-nip">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                            @endif
                            @if($pegawaiPlh && isset($pegawaiPlh->nip))
                            <div class="signature-nip">NIP. {{ $pegawaiPlh->nip }}</div>
                            @endif
                        @else
                            <div>{{ strtoupper($definitifJabatan) }},</div>
                            <div class="signature-name">{{ strtoupper($definitifNama) }}</div>
                            @if($definitifNip)
                            <div class="signature-nip">NIP. {{ $definitifNip }}</div>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN 2 - CATATAN PERJALANAN DINAS -->
    <div class="page-break">
        <table class="page2-table">
            <tr>
                <td class="page2-col-left">
                    <div class="page2-row-label"></div>
                    <div class="page2-row-label"><span class="page2-dotted"></span></div>
                    <div class="page2-row-label"> <span class="page2-dotted"></span></div>
                    <br>
        
                    
                </td>
                <td class="page2-col-right">
                    <div class="page2-field">
                        <span class="page2-field-label">Berangkat dari</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value">Mulia</span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Ke</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value">{{ $pengawasan->lokasi_penugasan }}</span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"></span>
                    </div>
                    <div style="text-align: center; font-size: 8pt; font-weight: bold; margin-top: 8px;">PEJABAT PELAKSANA TEKNIS KEGIATAN</div>
                    <br>
                    <div class="page2-dotted"></div>
                    <div style="text-align: center; font-size: 8pt;">( <span style="display: inline-block; width: 160px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div style="text-align: center; font-size: 8pt;">NIP. <span class="page2-dotted" style="display: inline-block; width: 130px;"></span></div>
                </td>
            </tr>
            <tr>
                <td class="page2-col-left">
                    <div class="page2-field">
                        <span class="page2-field-label">II. Tiba di</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div><br>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
                <td class="page2-col-right">
                    <div class="page2-field">
                        <span class="page2-field-label">Berangkat dari</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Ke</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="page2-col-left">
                    <div class="page2-field">
                        <span class="page2-field-label">III. Tiba di</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div><br>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
                <td class="page2-col-right">
                    <div class="page2-field">
                        <span class="page2-field-label">Berangkat dari</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Ke</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="page2-col-left">
                    <div class="page2-field">
                        <span class="page2-field-label">IV. Tiba di</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 130px;"></span></span>
                    </div><br>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
                <td class="page2-col-right">
                    <div class="page2-field">
                        <span class="page2-field-label">Berangkat dari</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Pada Tanggal</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Ke</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">Kepala</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div style="text-align: center; font-size: 8pt; margin-top: 5px;">( <span style="display: inline-block; width: 150px; border-bottom: 1px dotted #000;"></span> )</div>
                    <div class="page2-field" style="text-align: center; margin-top: 3px;">
                        <span class="page2-field-label" style="width: auto;">NIP.</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value"><span class="page2-dotted" style="display: inline-block; width: 120px;"></span></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="page2-col-left">
                    <div class="page2-field">
                        <span class="page2-field-label">VI. Tiba Kembali Di</span>
                        <span class="page2-field-colon">:</span>
                        <span class="page2-field-value">Mulia<span  style="display: inline-block; width: 110px;"></span></span>
                    </div>
                    <div class="page2-field">
                        <span class="page2-field-label">( Tempat Tujuan )</span>
                    </div>
                    <div style="text-align: center; font-size: 8pt; margin-top: 8px;"></div>
                </td>
                <td class="page2-col-right">
                    <div style="text-align: left; font-size: 8pt; font-weight:regular; margin-top: 5px; line-height: 1.1;">Telah diperiksa dengan keterangan bahwa perjalanan tersebut atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya</div>
                    @if($isPerjalananDinasInspektur && !$isPlh)
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">Pj. Sekretaris Daerah,</div>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">Kabupaten Puncak Jaya</div>
                        <br><br><br><br>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; text-decoration: underline;">Yubelina Enumbi, SE., MM., MH.</div>
                        <div style="text-align: left; font-size: 8pt;">Pembina Utama Muda</div>
                        <div style="text-align: left; font-size: 8pt;">NIP. 198111182004122001</div>
                    @elseif($isPerjalananDinasInspektur && $isPlh)
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">Plh. Sekretaris Daerah,</div>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">Kabupaten Puncak Jaya</div>
                        <br><br><br><br>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; text-decoration: underline;">{{ $pengawasan->penandatangan_plh_nama }}</div>
                        @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                        <div style="text-align: left; font-size: 8pt;">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                        @endif
                        @if($pegawaiPlh && isset($pegawaiPlh->nip))
                        <div style="text-align: left; font-size: 8pt;">NIP. {{ $pegawaiPlh->nip }}</div>
                        @endif
                    @elseif($isPlh)
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">Plh. INSPEKTUR,</div>
                        <br><br><br>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; text-decoration: underline;">{{ strtoupper($pengawasan->penandatangan_plh_nama) }}</div>
                        @if($pegawaiPlh && (isset($pegawaiPlh->pangkat) || isset($pegawaiPlh->golongan)))
                        <div style="text-align: left; font-size: 8pt;">{{ trim(explode('(', $pegawaiPlh->pangkat ?? $pegawaiPlh->golongan ?? '')[0]) }}</div>
                        @endif
                        @if($pegawaiPlh && isset($pegawaiPlh->nip))
                        <div style="text-align: left; font-size: 8pt;">NIP. {{ $pegawaiPlh->nip }}</div>
                        @endif
                    @else
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; margin-top: 5px;">{{ strtoupper($definitifJabatan) }},</div>
                        <br><br><br>
                        <div style="text-align: left; font-size: 8pt; font-weight: bold; text-decoration: underline;">{{ strtoupper($definitifNama) }}</div>
                        @if($definitifNip)
                        <div style="text-align: left; font-size: 8pt;">NIP. {{ $definitifNip }}</div>
                        @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="font-weight: bold; font-size: 8pt; margin-bottom: 2px; line-height: 1.2;">VII. Catatan Lain - Lain : .</div>
                </td>
            </tr>
        </table>

        <!-- CATATAN/PERHATIAN -->
        <div class="page2-footer">
            <div class="page2-footer-title">VIII. CATATAN/PERHATIAN</div>
            <div>NB. PERHATIAN :</div>
            <div>Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba, serta bendahara pengeluaran bertanggung jawab berdasarkan peraturan-peraturan Keuangan Negara apabila Negara menderita rugi akibat kesalahan, kelalaian, dan kecurangannya.</div>
        </div>
    </div>
</body>
</html>

