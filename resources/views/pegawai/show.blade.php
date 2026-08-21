@extends('layouts.app')

@section('title', 'Profil & Pengalaman Pegawai')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span>Master Data</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('pegawai.index') }}" class="hover:text-primary transition-colors">Data Pegawai</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Profil Pegawai</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Profil & Pengalaman Pegawai</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Lihat detail profil dan riwayat pengawasan dari pegawai bersangkutan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pegawai.index') }}" class="bg-surface-container-high text-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-surface-container-highest transition-colors focus:outline-none border border-outline-variant font-semibold text-[13px]">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>
            <a href="{{ route('pegawai.cetak-profil', $pegawai->id) }}" target="_blank" class="bg-primary text-on-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-primary/90 transition-colors shadow-sm focus:outline-none font-semibold text-[13px]">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak PDF
            </a>
        </div>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop flex flex-col gap-6">

    <!-- Profile Header Card -->
    <div class="bg-primary text-white rounded-xl shadow-md p-6 relative overflow-hidden">
        <div class="absolute top-[-50%] right-[-10%] w-[400px] h-[400px] bg-white/10 rounded-full"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            @php
                $bgColors = ['bg-blue-500 text-white','bg-violet-500 text-white','bg-teal-500 text-white','bg-rose-500 text-white','bg-amber-500 text-white'];
                $bgColor = $bgColors[abs(crc32($pegawai->nama ?? '')) % count($bgColors)];
            @endphp
            <div class="w-24 h-24 rounded-full border-4 border-white/20 {{ $bgColor }} shadow-lg flex items-center justify-center font-bold text-4xl shrink-0">
                {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl font-bold mb-3">{{ $pegawai->nama }}</h2>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-[13px] opacity-90">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        NIP: {{ $pegawai->nip }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">work</span>
                        {{ $pegawai->jabatan ?? '-' }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">military_tech</span>
                        {{ $pegawai->golongan ?? '-' }}
                    </div>
                    @if($pegawai->email)
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">mail</span>
                        {{ $pegawai->email }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-surface-card border border-border-subtle rounded-xl p-5 shadow-sm border-l-4 border-l-blue-500">
            <div class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Penugasan</div>
            <div class="text-[2rem] font-black text-primary leading-none">{{ $totalPenugasan }}</div>
        </div>
        <div class="bg-surface-card border border-border-subtle rounded-xl p-5 shadow-sm border-l-4 border-l-green-500">
            <div class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Selesai</div>
            <div class="text-[2rem] font-black text-green-600 leading-none">{{ $totalSelesai }}</div>
        </div>
        <div class="bg-surface-card border border-border-subtle rounded-xl p-5 shadow-sm border-l-4 border-l-rose-500">
            <div class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Belum Selesai</div>
            <div class="text-[2rem] font-black text-rose-600 leading-none">{{ $totalBelumSelesai }}</div>
        </div>
    </div>

    @if($totalPenugasan == 0)
    <div class="bg-surface-card border border-border-subtle rounded-xl p-10 flex flex-col items-center justify-center text-center shadow-sm">
        <span class="material-symbols-outlined text-[48px] text-on-surface-variant opacity-30 mb-3">assignment_late</span>
        <h3 class="font-bold text-label-lg text-primary mb-1">Belum Ada Penugasan</h3>
        <p class="text-[13px] text-on-surface-variant">Pegawai ini belum memiliki riwayat penugasan.</p>
    </div>
    @endif

    @php
        $roles = [
            ['title' => 'Sebagai Penanggung Jawab', 'data' => $pengawasanAsPJ],
            ['title' => 'Sebagai Pengendali Teknis', 'data' => $pengawasanAsPT],
            ['title' => 'Sebagai Ketua Tim', 'data' => $pengawasanAsKetua],
            ['title' => 'Sebagai Anggota Tim', 'data' => $pengawasanAsAnggota]
        ];
    @endphp

    @foreach($roles as $role)
        @if($role['data']->count() > 0)
        <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
            <div class="px-5 py-4 border-b border-border-subtle flex justify-between items-center bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
                    </div>
                    <h2 class="font-bold text-label-lg text-primary">{{ $role['title'] }}</h2>
                </div>
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-[11px] font-bold">
                    {{ $role['data']->count() }} Penugasan
                </span>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4 border-b border-border-subtle text-center w-12">No</th>
                            <th class="py-3 px-4 border-b border-border-subtle w-1/3">Uraian & Nomor ST</th>
                            <th class="py-3 px-4 border-b border-border-subtle">Jenis</th>
                            <th class="py-3 px-4 border-b border-border-subtle">Periode & Durasi</th>
                            <th class="py-3 px-4 border-b border-border-subtle">Lokasi</th>
                            <th class="py-3 px-4 border-b border-border-subtle">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-on-surface divide-y divide-border-subtle">
                        @foreach($role['data'] as $index => $p)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="py-3 px-4 text-center font-semibold text-on-surface-variant">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-primary mb-1">{{ $p->uraian_penugasan }}</div>
                                <span class="font-mono text-[11px] bg-surface-container-high px-2 py-0.5 rounded text-on-surface-variant">
                                    {{ $p->nomor_st }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                                    {{ $p->jenis_penugasan }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($p->tanggal_st)->format('d M Y') }}</div>
                                <div class="text-[11px] text-on-surface-variant">{{ $p->lama_penugasan }} Hari</div>
                            </td>
                            <td class="py-3 px-4">{{ $p->lokasi_penugasan }}</td>
                            <td class="py-3 px-4">
                                @if($p->status == 'selesai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-green-500/10 text-green-700 border border-green-500/20">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                                        Belum Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach

</div>
@endsection
