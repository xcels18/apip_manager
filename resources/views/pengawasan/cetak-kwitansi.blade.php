@php
    use Carbon\Carbon;
    $definitifNama = \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTEN TANDIPADA,ST.,M.AP';
    $definitifNip = \App\Models\SystemSetting::where('key', 'definitif_nip')->first()->value ?? '19780218 200012 1 002';
    $definitifJabatan = \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. Inspektur';
    $definitifPangkatRaw = \App\Models\SystemSetting::where('key', 'definitif_pangkat')->first()->value ?? '';
    $definitifPangkat = trim(explode('(', $definitifPangkatRaw)[0]);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi_{{ $pegawai->nama }}_{{$pengawasan->nomor_st}}</title>
    <style type="text/css">
        @page {
            size: A5 landscape;
            margin: 0.8cm 1.2cm 0.8cm 1.2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .kwitansi-container {
            border: 2px solid #000;
            padding: 10px;
            margin: 0 auto;
        }

        /* KOP Surat */
        .kop-surat {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
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
            width: 45px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo-left img {
            width: 40px;
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
            font-size: 11pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .kop-subheader {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .kop-alamat {
            font-size: 7pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .kwitansi-header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
        }

        .kwitansi-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            line-height: 1;
        }

        .kwitansi-subtitle {
            font-size: 8pt;
            color: #666;
            line-height: 1;
        }

        .kwitansi-body {
            margin: 8px 0;
        }

        .kwitansi-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .kwitansi-label {
            display: table-cell;
            width: 28%;
            font-weight: 600;
            vertical-align: top;
            font-size: 8pt;
            line-height: 1.2;
        }

        .kwitansi-colon {
            display: table-cell;
            width: 2%;
            vertical-align: top;
            font-size: 8pt;
        }

        .kwitansi-value {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            font-size: 8pt;
            line-height: 1.2;
        }

        .nominal-box {
            border: 2px solid #000;
            padding: 0;
            margin: 8px 0;
            position: relative;
        }

        .nominal-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        .nominal-row:last-child {
            border-bottom: none;
        }

        .nominal-label-cell {
            display: table-cell;
            width: 20%;
            font-weight: 600;
            vertical-align: middle;
            font-size: 8pt;
            padding: 5px 8px;
            border-right: 1px solid #000;
            line-height: 1.2;
        }

        .nominal-value-cell {
            display: table-cell;
            width: 80%;
            vertical-align: middle;
            padding: 5px 8px;
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                #000 2px,
                #000 3px
            );
            background-size: 100% 6px;
        }

        .nominal-angka {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .nominal-terbilang {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            font-style: italic;
            line-height: 1.2;
        }

        .signature-section {
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .signature-left {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .signature-center {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .signature-right {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 8pt;
            line-height: 1.2;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8pt;
            line-height: 1.2;
        }

        .signature-nip {
            font-size: 7pt;
            margin-top: 1px;
            line-height: 1.2;
        }

        .kwitansi-footer {
            margin-top: 8px;
            font-size: 6pt;
            color: #666;
            text-align: left;
            border-top: 1px solid #ddd;
            padding-top: 3px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    @php
        // Check if this is Perjalanan Dinas with Plt. Inspektur
        $isPerjalananDinasInspektur = false;

        if ($pengawasan->jenis_penugasan === 'Perjalanan Dinas Luar Daerah') {
            // Check if the current pegawai is Plt. Inspektur (BOTTEN TANDIPADA)
            if (stripos($pegawai->nama ?? '', 'BOTTEN TANDIPADA') !== false) {
                $isPerjalananDinasInspektur = true;
            }
        }
    @endphp

    <div class="kwitansi-container">
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
                        <div class="kop-header">PEMERINTAH KABUPATEN PUNCAK JAYA</div>
                        @if($isPerjalananDinasInspektur)
                            <div class="kop-subheader">SEKRETARIAT DAERAH</div>
                            <div class="kop-alamat">Jl. Drs.P.A.Coem No.01 Mulia, Puncak Jaya</div>
                        @else
                            <div class="kop-subheader">INSPEKTORAT</div>
                            <div class="kop-alamat">Jl. Drs.P.A.Coem No.01 Mulia, Puncak Jaya</div>
                            <div class="kop-alamat">Email: inspektorat@puncakjayakab.go.id</div>
                        @endif
                    </td>
                    <td class="kop-logo-left"></td>
                </tr>
            </table>
        </div>

        <div class="kwitansi-header">
            <div class="kwitansi-title">Kwitansi</div>
        </div>

        <div class="kwitansi-body">
            <div class="kwitansi-row">
                <div class="kwitansi-label">Sudah Terima Dari</div>
                <div class="kwitansi-colon">:</div>
                <div class="kwitansi-value">Bendahara Pengeluaran Inspektorat Daerah Kab. Puncak Jaya</div>
            </div>

            <div class="nominal-box">
                <div class="nominal-row">
                    <div class="nominal-label-cell">Jumlah</div>
                    <div class="nominal-value-cell">
                        <div class="nominal-angka">Rp {{ number_format($nominal, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="nominal-row">
                    <div class="nominal-label-cell">Terbilang</div>
                    <div class="nominal-value-cell">
                        <div class="nominal-terbilang">{{ ucwords(\App\Helpers\Terbilang::convert($nominal)) }} Rupiah</div>
                    </div>
                </div>
            </div>

            <div class="kwitansi-row">
                <div class="kwitansi-label">Untuk Pembayaran</div>
                <div class="kwitansi-colon">:</div>
                <div class="kwitansi-value">{{ $pengawasan->uraian_penugasan }} di {{ $pengawasan->lokasi_penugasan }} ({{ $pengawasan->lama_penugasan }} Hari)</div>
            </div>

            <div class="kwitansi-row">
                <div class="kwitansi-label">Nomor/Tanggal ST</div>
                <div class="kwitansi-colon">:</div>
                <div class="kwitansi-value">{{ $pengawasan->nomor_st ?? '-' }}, {{ $pengawasan->tanggal_st->format('d-m-Y') }}</div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-left">
                <div class="signature-title">&nbsp;</div>
                <div class="signature-title">Mengetahui, <br>{{ $definitifJabatan }},</div>
                <div class="signature-name">{{ strtoupper($definitifNama) }}</div>
                @if($definitifPangkat)
                <div class="signature-nip">{{ $definitifPangkat }}</div>
                @endif
                @if($definitifNip)
                <div class="signature-nip">NIP. {{ $definitifNip }}</div>
                @endif
            </div>
            <div class="signature-center">
                <div class="signature-title">&nbsp;</div>
               
                <div class="signature-title">Bendahara Pengeluaran,</div><br>
                <div class="signature-name">ARIPIN SIBARANI, SE</div>
                <div class="signature-nip">NIP. 19820728 200412 1 001</div>
            </div>
            <div class="signature-right">

                <div class="signature-title">Mulia,________________{{ now()->format('Y') }}</div>
                <div class="signature-title">Yang Menerima,</div><br>
                <div class="signature-name">{{ strtoupper($pegawai->nama ?? '-') }}</div>
                <div class="signature-nip">NIP. {{ $pegawai->nip ?? '-' }}</div>
            </div>
        </div>

        <div class="kwitansi-footer">
            Dokumen ini dicetak secara otomatis dari Sistem Informasi Pengawasan Inspektorat Kabupaten Puncak Jaya
        </div>
    </div>
</body>
</html>

