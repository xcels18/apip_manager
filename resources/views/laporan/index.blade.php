@extends('layouts.app')

@section('title', 'Laporan Pengawasan')

@section('content')
@extends('layouts.app')

@section('title', 'Laporan Pengawasan')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-on-surface font-bold">Laporan Selesai</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-on-surface">Arsip Laporan</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Dokumen hasil pengawasan yang telah selesai dan siap diunduh.</p>
        </div>
        
        <div class="bg-surface-card border border-border-subtle rounded-lg px-4 py-2 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-md bg-on-surface text-on-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">folder_copy</span>
            </div>
            <div>
                <div class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Total Laporan</div>
                <div class="text-[16px] font-black text-on-surface leading-none mt-0.5">{{ $pengawasan->total() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="px-margin-desktop pb-6">
    <form method="GET" action="{{ route('laporan.index') }}" id="filterForm" class="bg-surface-card border border-border-subtle rounded-xl p-4 flex flex-col md:flex-row flex-wrap gap-4 shadow-sm">
        
        <!-- Filter Tahun -->
        <div class="w-full md:w-auto min-w-[200px]">
            <label class="block text-label-sm font-label-sm text-on-surface-variant mb-1">Tahun Pengawasan</label>
            <div class="relative">
                <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="w-full h-10 px-3 pr-8 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-on-surface focus:border-on-surface focus:outline-none bg-surface-container-lowest appearance-none cursor-pointer text-on-surface">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
            </div>
        </div>

        <!-- Filter Jenis -->
        <div class="w-full md:w-auto min-w-[240px]">
            <label class="block text-label-sm font-label-sm text-on-surface-variant mb-1">Jenis Pemeriksaan</label>
            <div class="relative">
                <select name="jenis" onchange="document.getElementById('filterForm').submit()" class="w-full h-10 px-3 pr-8 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-on-surface focus:border-on-surface focus:outline-none bg-surface-container-lowest appearance-none cursor-pointer text-on-surface">
                    <option value="">Semua Jenis Pemeriksaan</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j }}" {{ request('jenis') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
            </div>
        </div>

        @if(request('tahun') || request('jenis'))
            <div class="flex items-end">
                <a href="{{ route('laporan.index') }}" class="h-10 px-4 text-rose-600 text-[13px] font-semibold hover:bg-rose-50 rounded-lg transition-colors flex items-center justify-center gap-2 border border-rose-200 bg-surface-card">
                    <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                    Reset Filter
                </a>
            </div>
        @endif
    </form>
</div>

<!-- Content Section -->
<div class="px-margin-desktop pb-margin-desktop flex-1 flex flex-col h-full">
    @if($pengawasan->count() > 0)
        <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-border-subtle text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">
                            <th class="px-4 py-3 font-bold">No. ST / Tanggal</th>
                            <th class="px-4 py-3 font-bold">Jenis</th>
                            <th class="px-4 py-3 font-bold max-w-xs">Uraian Penugasan</th>
                            <th class="px-4 py-3 font-bold">Lokasi</th>
                            <th class="px-4 py-3 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @foreach($pengawasan as $index => $p)
                            <tr class="hover:bg-surface-container-lowest transition-colors group">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-bold text-[13px] text-on-surface mb-0.5">{{ $p->nomor_st }}</div>
                                    <div class="text-[11px] text-on-surface-variant flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                                        {{ $p->tanggal_st->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-surface-container-high text-on-surface border border-border-subtle">
                                        {{ $p->jenis_penugasan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top max-w-xs">
                                    <div class="text-[12px] text-on-surface leading-snug line-clamp-2" title="{{ $p->uraian_penugasan }}">
                                        {{ $p->uraian_penugasan }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top text-[12px] text-on-surface-variant truncate max-w-[150px]" title="{{ $p->lokasi_penugasan }}">
                                    {{ $p->lokasi_penugasan }}
                                </td>
                                <td class="px-4 py-3 align-top text-right">
                                    <a href="{{ asset('storage/' . $p->file_laporan) }}" target="_blank" 
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-on-surface hover:bg-on-surface/90 text-on-primary rounded-lg text-[11px] font-bold transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($pengawasan->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $pengawasan->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-surface-card border border-border-subtle rounded-xl p-16 flex flex-col items-center justify-center text-center shadow-sm w-full h-full min-h-[300px]">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant opacity-30 mb-3">folder_off</span>
            <h2 class="font-bold text-[16px] text-on-surface mb-1">Belum Ada Laporan</h2>
            <p class="text-[13px] text-on-surface-variant max-w-sm">Dokumen laporan akan muncul di sini setelah status pengawasan diselesaikan dan file laporan diunggah.</p>
        </div>
    @endif
</div>
@endsection
