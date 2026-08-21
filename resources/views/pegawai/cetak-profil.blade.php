<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Profil & Pengalaman - {{ $pegawai->nama }}</title>
    <style type="text/css">
        @page {
            margin: 1cm 2cm 1.5cm 2cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .kop-logo-right {
            width: 35px;
        }

        .kop-header {
            font-weight: bold;
            font-size: 14pt;
            margin: 0;
            padding: 1px 0;
        }

        .kop-subheader {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 1px 0;
        }

        .kop-alamat {
            font-size: 8pt;
            margin: 0;
            padding: 1px 0;
        }

        .judul {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 12px 0 8px 0;
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
            width: 120px;
            font-weight: bold;
        }

        .colon {
            width: 12px;
            text-align: center;
        }

        .section-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 12px 0 10px 0;
            padding: 0;
        }

        .stats-box {
            width: 100%;
            margin: 10px 0;
            text-align: center;
        }

        .stats-inline {
            display: inline-block;
            margin: 0 15px;
            font-size: 10pt;
        }

        .stats-inline strong {
            font-size: 12pt;
        }

        .penugasan-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
        }

        .penugasan-table th,
        .penugasan-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 8pt;
            line-height: 1.3;
        }

        .penugasan-table th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .penugasan-table td {
            vertical-align: top;
        }

        .penugasan-uraian {
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 8pt;
        }

        .penugasan-nomor {
            font-size: 7pt;
            font-style: italic;
            color: #333;
        }

        .footer {
            position: fixed;
            bottom: 10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .page-number:after {
            content: counter(page);
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            font-style: italic;
        }

        /* Status Colors */
        .status-selesai {
            color: #059669;
            font-weight: bold;
        }

        .status-belum {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-left">
                    @php
                    $logoPath = public_path('images/logo_puja.png');
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $imageData = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/png;base64,' . base64_encode($imageData);
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
                    <div class="kop-header">PEMERINTAH KABUPATEN PUNCAK JAYA</div>
                    <div class="kop-subheader">INSPEKTORAT</div>
                    <div class="kop-alamat">Jalan Yos Sudarso Kotaraja Telp. (0969) 31001 Kode Pos 99531</div>
                    <div class="kop-alamat">Email: inspektorat@puncakjayakab.go.id</div>
                </td>
                <td class="kop-logo-right">
                    <!-- Spacer untuk balance -->
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL -->
    <div class="judul">PROFIL DAN RIWAYAT PENUGASAN PENGAWASAN</div>

    <!-- DATA PEGAWAI -->
    <table class="content-table">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td>{{ $pegawai->nama }}</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="colon">:</td>
            <td>{{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td>{{ $pegawai->jabatan }}</td>
        </tr>
        <tr>
            <td class="label">Pangkat/Golongan</td>
            <td class="colon">:</td>
            <td>{{ $pegawai->golongan }}</td>
        </tr>
        @if($pegawai->email)
        <tr>
            <td class="label">Email</td>
            <td class="colon">:</td>
            <td>{{ $pegawai->email }}</td>
        </tr>
        @endif
    </table>

    <!-- STATISTIK -->
    <div class="stats-box">
        <span class="stats-inline">Total Penugasan: <strong>{{ $totalPenugasan }}</strong></span> |
        <span class="stats-inline">Selesai: <strong>{{ $totalSelesai }}</strong></span> |
        <span class="stats-inline">Belum Selesai: <strong>{{ $totalBelumSelesai }}</strong></span>
    </div>

    <!-- Penugasan sebagai Penanggung Jawab -->
    @if($pengawasanAsPJ->count() > 0)
    <div class="section-title">SEBAGAI PENANGGUNG JAWAB ({{ $pengawasanAsPJ->count() }} Penugasan)</div>
    <table class="penugasan-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 38%;">Uraian & Nomor ST</th>
                <th style="width: 13%;">Jenis</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 7%;">Durasi</th>
                <th style="width: 18%;">Lokasi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengawasanAsPJ as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="penugasan-uraian">{{ $p->uraian_penugasan }}</div>
                    <div class="penugasan-nomor">{{ $p->nomor_st }}</div>
                </td>
                <td>{{ $p->jenis_penugasan }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($p->tanggal_st)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $p->lama_penugasan }} hari</td>
                <td>{{ $p->lokasi_penugasan }}</td>
                <td style="text-align: center;">
                    <span class="{{ $p->status == 'selesai' ? 'status-selesai' : 'status-belum' }}">
                        {{ $p->status == 'selesai' ? 'Selesai' : 'Belum Selesai' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Penugasan sebagai Pengendali Teknis -->
    @if($pengawasanAsPT->count() > 0)
    <div class="section-title">SEBAGAI PENGENDALI TEKNIS ({{ $pengawasanAsPT->count() }} Penugasan)</div>
    <table class="penugasan-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 38%;">Uraian & Nomor ST</th>
                <th style="width: 13%;">Jenis</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 7%;">Durasi</th>
                <th style="width: 18%;">Lokasi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengawasanAsPT as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="penugasan-uraian">{{ $p->uraian_penugasan }}</div>
                    <div class="penugasan-nomor">{{ $p->nomor_st }}</div>
                </td>
                <td>{{ $p->jenis_penugasan }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($p->tanggal_st)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $p->lama_penugasan }} hari</td>
                <td>{{ $p->lokasi_penugasan }}</td>
                <td style="text-align: center;">
                    <span class="{{ $p->status == 'selesai' ? 'status-selesai' : 'status-belum' }}">
                        {{ $p->status == 'selesai' ? 'Selesai' : 'Belum Selesai' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Penugasan sebagai Ketua Tim -->
    @if($pengawasanAsKetua->count() > 0)
    <div class="section-title">SEBAGAI KETUA TIM ({{ $pengawasanAsKetua->count() }} Penugasan)</div>
    <table class="penugasan-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 38%;">Uraian & Nomor ST</th>
                <th style="width: 13%;">Jenis</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 7%;">Durasi</th>
                <th style="width: 18%;">Lokasi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengawasanAsKetua as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="penugasan-uraian">{{ $p->uraian_penugasan }}</div>
                    <div class="penugasan-nomor">{{ $p->nomor_st }}</div>
                </td>
                <td>{{ $p->jenis_penugasan }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($p->tanggal_st)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $p->lama_penugasan }} hari</td>
                <td>{{ $p->lokasi_penugasan }}</td>
                <td style="text-align: center;">
                    <span class="{{ $p->status == 'selesai' ? 'status-selesai' : 'status-belum' }}">
                        {{ $p->status == 'selesai' ? 'Selesai' : 'Belum Selesai' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Penugasan sebagai Anggota Tim -->
    @if($pengawasanAsAnggota->count() > 0)
    <div class="section-title">SEBAGAI ANGGOTA TIM ({{ $pengawasanAsAnggota->count() }} Penugasan)</div>
    <table class="penugasan-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 38%;">Uraian & Nomor ST</th>
                <th style="width: 13%;">Jenis</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 7%;">Durasi</th>
                <th style="width: 18%;">Lokasi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengawasanAsAnggota as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="penugasan-uraian">{{ $p->uraian_penugasan }}</div>
                    <div class="penugasan-nomor">{{ $p->nomor_st }}</div>
                </td>
                <td>{{ $p->jenis_penugasan }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($p->tanggal_st)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $p->lama_penugasan }} hari</td>
                <td>{{ $p->lokasi_penugasan }}</td>
                <td style="text-align: center;">
                    <span class="{{ $p->status == 'selesai' ? 'status-selesai' : 'status-belum' }}">
                        {{ $p->status == 'selesai' ? 'Selesai' : 'Belum Selesai' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($totalPenugasan == 0)
    <div class="empty-state">
        <p>Belum ada riwayat penugasan untuk pegawai ini.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIT | Halaman <span class="page-number"></span>
    </div>
</body>
</html>

