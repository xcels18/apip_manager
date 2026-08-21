@extends('layouts.app')

@section('title', 'Direktori Pegawai')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span>Master Data</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Data Pegawai</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Direktori Pegawai</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Kelola data pegawai, jabatan, dan kepangkatan di lingkungan Inspektorat.</p>
        <div class="flex items-center gap-3">
            <!-- Read-only mode: management is done in DBASN -->
            <span class="bg-surface-container-low text-on-surface-variant h-10 px-4 rounded-lg flex items-center gap-2 border border-outline-variant font-semibold text-[13px]">
                <span class="material-symbols-outlined text-[20px]">info</span>
                Data dikelola via API DBASN
            </span>
        </div>
    </div>
</div>

<!-- Alerts -->
<div class="px-margin-desktop mb-4">
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="text-[13px] font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span class="text-[13px] font-medium">{{ session('error') }}</span>
        </div>
    @endif
</div>

<!-- Filter Bar -->
<div class="px-margin-desktop pb-6">
    <form method="GET" action="{{ route('pegawai.index') }}" class="bg-surface-card border border-border-subtle rounded-xl p-4 flex flex-wrap gap-4 items-end shadow-sm">
        <!-- Search -->
        <div class="flex-1 min-w-[240px]">
            <label class="block text-label-sm font-label-sm text-on-surface-variant mb-1">Cari Pegawai</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input name="search" value="{{ $search ?? '' }}" class="w-full h-10 pl-10 pr-4 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none bg-surface-container-lowest" placeholder="Ketik nama atau NIP..." type="text"/>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="h-10 px-6 bg-secondary-fixed text-on-secondary-fixed font-semibold text-[13px] rounded-lg transition-colors hover:bg-secondary-fixed/90 border border-secondary-fixed">
                Cari
            </button>
            <!-- Clear Filter -->
            <a href="{{ route('pegawai.index') }}" class="h-10 px-4 text-on-surface-variant text-[13px] font-semibold hover:bg-surface-container-low rounded-lg transition-colors flex items-center justify-center gap-2 border border-outline-variant bg-surface-card">
                <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Data Table Section -->
<div class="px-margin-desktop flex-1 pb-margin-desktop flex flex-col h-full">
    
    @if($search ?? false)
        <div class="mb-4 text-[13px] text-on-surface-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">info</span>
            Menemukan <strong>{{ $pegawai->total() }}</strong> hasil pencarian untuk "<strong>{{ $search }}</strong>"
        </div>
    @endif

    <div class="border border-border-subtle rounded-xl flex flex-col bg-surface-card overflow-hidden shadow-sm">
        <!-- Table Wrapper for Scroll -->
        <div class="overflow-x-auto w-full">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="text-[11px] font-bold text-on-surface-variant bg-surface-container-low uppercase tracking-wider">
                        <th class="w-12 text-center py-3 border-b border-border-subtle">No</th>
                        <th class="py-3 px-4 border-b border-border-subtle">Pegawai</th>
                        <th class="py-3 px-4 border-b border-border-subtle">NIP</th>
                        <th class="py-3 px-4 border-b border-border-subtle">Jabatan</th>
                        <th class="py-3 px-4 border-b border-border-subtle">Golongan</th>
                    </tr>
                </thead>
                <tbody class="text-[13px] text-on-surface divide-y divide-border-subtle">
                    @forelse($pegawai as $index => $p)
                        @php
                            $bgColors = ['bg-blue-500/20 text-blue-700','bg-violet-500/20 text-violet-700','bg-teal-500/20 text-teal-700','bg-rose-500/20 text-rose-700','bg-amber-500/20 text-amber-700'];
                            $bgColor = $bgColors[abs(crc32($p->nama ?? '')) % count($bgColors)];
                        @endphp
                        <tr class="hover:bg-surface-container-lowest transition-colors group">
                            <td class="text-center py-3 font-semibold text-on-surface-variant">{{ $pegawai->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full overflow-hidden {{ $bgColor }} shrink-0 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($p->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-primary">
                                            {{ $p->nama }}
                                        </div>
                                        <div class="text-on-surface-variant text-[11px] mt-0.5">{{ $p->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[12px] text-on-surface-variant">{{ $p->nip }}</td>
                            <td class="py-3 px-4">{{ $p->jabatan }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-green-500/10 text-green-700 border border-green-500/20">
                                    {{ $p->golongan }}
                                </span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-[48px] block mb-3 opacity-20">group_off</span>
                                <div class="font-bold text-[14px] text-on-surface">Tidak ada data pegawai</div>
                                <div class="text-[12px] mt-1">Belum ada pegawai yang ditambahkan atau tidak sesuai kriteria pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pegawai->hasPages())
            <div class="px-4 py-3 border-t border-border-subtle bg-surface-card flex items-center justify-between">
                {{ $pegawai->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

@endsection
