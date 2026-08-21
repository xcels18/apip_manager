@extends('layouts.app')

@section('title', 'Rekap Data Pengawasan')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Rekap Data</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Rekap Data Pengawasan</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Filter dan unduh data rekapitulasi pengawasan dalam bentuk Excel.</p>
        </div>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop flex flex-col gap-6">

    <!-- Info Card -->
    <div class="bg-primary/5 border border-primary/20 rounded-xl p-5 flex flex-col gap-3">
        <div class="flex items-center gap-2 text-primary font-bold text-label-lg">
            <span class="material-symbols-outlined text-[20px]">info</span>
            Informasi Export Data
        </div>
        <ul class="flex flex-col gap-2 text-[13px] text-on-surface font-medium">
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>Data yang diexport mencakup semua informasi pengawasan lengkap</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>Format file: Microsoft Excel (.xlsx)</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>Gunakan filter untuk menyaring data sesuai kebutuhan</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>Data meliputi: Nomor ST, Tanggal, Jenis, Uraian, Lokasi, Status, Personil, dan Dasar Hukum</li>
        </ul>
    </div>

    <!-- Filter Card -->
    <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[24px]">filter_alt</span>
            <h2 class="font-bold text-label-lg text-primary">Filter Data</h2>
        </div>
        
        <form action="{{ route('rekap.export') }}" method="GET" class="p-6 bg-white flex flex-col gap-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                
                <div class="flex flex-col gap-2">
                    <label class="text-[13px] font-bold text-on-surface">Status</label>
                    <select name="status" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors">
                        <option value="">Semua Status</option>
                        <option value="belum_selesai">Belum Selesai</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[13px] font-bold text-on-surface">Jenis Penugasan</label>
                    <select name="jenis" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors">
                        <option value="">Semua Jenis</option>
                        <option value="Audit">Audit</option>
                        <option value="Reviu">Reviu</option>
                        <option value="Monitoring">Monitoring</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="Pendampingan">Pendampingan</option>
                        <option value="Perjalanan Dinas Luar Daerah">Perjalanan Dinas Luar Daerah</option>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[13px] font-bold text-on-surface">Bulan</label>
                    <select name="bulan" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors">
                        <option value="">Semua Bulan</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[13px] font-bold text-on-surface">Tahun</label>
                    <select name="tahun" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 h-10 rounded-lg font-bold text-[13px] bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm focus:outline-none">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Download Excel
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
