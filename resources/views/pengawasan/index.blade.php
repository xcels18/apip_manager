@extends('layouts.app')

@section('title', 'Data Pengawasan')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Data Pengawasan</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Data Pengawasan</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Kelola data penugasan, monitoring, dan reviu pengawasan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pengawasan.create') }}" class="bg-primary text-on-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-primary/90 transition-colors shadow-sm focus:outline-none font-semibold text-[13px]">
                <span class="material-symbols-outlined text-[20px]">add_box</span>
                Tambah Pengawasan
            </a>
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
    <form method="GET" action="{{ route('pengawasan.index') }}" id="filterForm" class="bg-surface-card border border-border-subtle rounded-xl p-4 flex flex-col md:flex-row flex-wrap gap-4 md:items-end shadow-sm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        
        <!-- Search -->
        <div class="flex-1 min-w-[240px]">
            <label class="block text-label-sm font-label-sm text-on-surface-variant mb-1">Pencarian</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input name="search" value="{{ request('search') }}" oninput="debounceSubmit()" class="w-full h-10 pl-10 pr-4 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none bg-surface-container-lowest" placeholder="Nomor ST, Uraian, Lokasi..." type="text"/>
            </div>
        </div>
        
        <!-- Filter Jenis -->
        <div class="w-full md:w-auto">
            <label class="block text-label-sm font-label-sm text-on-surface-variant mb-1">Jenis Penugasan</label>
            <div class="relative">
                <select name="jenis" onchange="document.getElementById('filterForm').submit()" class="w-full md:w-56 h-10 px-3 pr-8 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none bg-surface-container-lowest appearance-none cursor-pointer text-on-surface">
                    <option value="">Semua Jenis Penugasan</option>
                    @foreach($jenisList as $jenis)
                        <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
            </div>
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            @if(request('search') || request('jenis'))
                <a href="{{ route('pengawasan.index', array_filter(['status' => request('status')])) }}" class="h-10 px-4 text-rose-600 text-[13px] font-semibold hover:bg-rose-50 rounded-lg transition-colors flex items-center justify-center gap-2 border border-rose-200 bg-surface-card">
                    <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                    Reset Filter
                </a>
            @endif
            
            <!-- View Toggle -->
            <div class="flex bg-surface-container-low p-1 rounded-lg border border-border-subtle h-10 shrink-0">
                <button type="button" class="w-10 h-full flex items-center justify-center rounded-md transition-colors" id="btnCard" onclick="setView('card')" title="Tampilan Card">
                    <span class="material-symbols-outlined text-[20px]">grid_view</span>
                </button>
                <button type="button" class="w-10 h-full flex items-center justify-center rounded-md transition-colors" id="btnList" onclick="setView('list')" title="Tampilan List">
                    <span class="material-symbols-outlined text-[20px]">view_list</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Status Filter Tabs -->
<div class="px-margin-desktop pb-4">
    <div class="flex flex-wrap gap-2">
        <span class="text-label-sm font-bold text-on-surface-variant flex items-center mr-2">Status:</span>
        <a href="{{ route('pengawasan.index', array_filter(['search' => request('search'), 'jenis' => request('jenis')])) }}"
           class="px-4 py-1.5 rounded-full text-[12px] font-bold transition-colors border {{ !request('status') ? 'bg-primary text-white border-primary' : 'bg-surface-card text-on-surface-variant border-border-subtle hover:bg-surface-container-low' }} flex items-center gap-1.5">
            Semua
            <span class="{{ !request('status') ? 'bg-white/20' : 'bg-surface-container-highest' }} px-1.5 py-0.5 rounded text-[10px]">{{ $totalSemua }}</span>
        </a>
        <a href="{{ route('pengawasan.index', array_filter(['status' => 'belum_selesai', 'search' => request('search'), 'jenis' => request('jenis')])) }}"
           class="px-4 py-1.5 rounded-full text-[12px] font-bold transition-colors border {{ request('status') == 'belum_selesai' ? 'bg-rose-600 text-white border-rose-600' : 'bg-surface-card text-on-surface-variant border-border-subtle hover:bg-surface-container-low' }} flex items-center gap-1.5">
            Belum Selesai
            <span class="{{ request('status') == 'belum_selesai' ? 'bg-white/20' : 'bg-surface-container-highest' }} px-1.5 py-0.5 rounded text-[10px]">{{ $totalBelumSelesai }}</span>
        </a>
        <a href="{{ route('pengawasan.index', array_filter(['status' => 'selesai', 'search' => request('search'), 'jenis' => request('jenis')])) }}"
           class="px-4 py-1.5 rounded-full text-[12px] font-bold transition-colors border {{ request('status') == 'selesai' ? 'bg-green-600 text-white border-green-600' : 'bg-surface-card text-on-surface-variant border-border-subtle hover:bg-surface-container-low' }} flex items-center gap-1.5">
            Selesai
            <span class="{{ request('status') == 'selesai' ? 'bg-white/20' : 'bg-surface-container-highest' }} px-1.5 py-0.5 rounded text-[10px]">{{ $totalSelesai }}</span>
        </a>
    </div>
</div>

<!-- Main Content Area -->
<div class="px-margin-desktop flex-1 pb-margin-desktop flex flex-col h-full">
    @if($pengawasan->count() > 0)
        <!-- CARD VIEW -->
        <div id="viewCard">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($pengawasan as $index => $item)
                    @php
                        $badgeClass = 'bg-blue-500/10 text-blue-700 border-blue-500/20';
                        if($item->jenis_penugasan == 'Reviu') $badgeClass = 'bg-amber-500/10 text-amber-700 border-amber-500/20';
                        elseif($item->jenis_penugasan == 'Monitoring') $badgeClass = 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20';
                        elseif($item->jenis_penugasan == 'Evaluasi') $badgeClass = 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20';
                        elseif($item->jenis_penugasan == 'Pendampingan') $badgeClass = 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20';
                        elseif($item->jenis_penugasan == 'Perjalanan Dinas Luar Daerah') $badgeClass = 'bg-pink-500/10 text-pink-700 border-pink-500/20';
                    @endphp
                    <div class="bg-surface-card border border-border-subtle rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary-fixed transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform"></div>
                        
                        <div class="flex justify-between items-start mb-4 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-primary font-bold text-[14px] leading-tight">{{ $item->nomor_st }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badgeClass }} whitespace-nowrap">
                                        {{ $item->jenis_penugasan }}
                                    </span>
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full text-white {{ $item->status == 'selesai' ? 'bg-green-600' : 'bg-rose-600' }} whitespace-nowrap">
                                        {{ $item->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Tanggal ST</span>
                                <span class="text-[12px] font-medium text-on-surface">{{ $item->tanggal_st->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Lama</span>
                                <span class="text-[12px] font-medium text-on-surface">{{ $item->lama_penugasan }} Hari</span>
                            </div>
                            <div class="flex flex-col col-span-2">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Lokasi</span>
                                <span class="text-[12px] font-medium text-on-surface truncate">{{ $item->lokasi_penugasan }}</span>
                            </div>
                            <div class="flex flex-col col-span-2">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Uraian</span>
                                <span class="text-[12px] text-on-surface-variant line-clamp-2">{{ $item->uraian_penugasan }}</span>
                            </div>
                        </div>

                        <div class="bg-surface-container-lowest rounded-lg p-3 border border-border-subtle text-[11px] space-y-1.5 mb-4">
                            @if($item->penanggungJawab)
                                <div class="flex"><span class="w-[90px] font-bold text-primary shrink-0">Penang. Jawab</span><span class="px-1 text-primary shrink-0">:</span><span class="text-on-surface truncate">{{ $item->penanggungJawab->nama }}</span></div>
                            @endif
                            @if($item->pengendaliTeknis)
                                <div class="flex"><span class="w-[90px] font-bold text-primary shrink-0">Peng. Teknis</span><span class="px-1 text-primary shrink-0">:</span><span class="text-on-surface truncate">{{ $item->pengendaliTeknis->nama }}</span></div>
                            @endif
                            @if($item->ketuaTim)
                                <div class="flex"><span class="w-[90px] font-bold text-primary shrink-0">Ketua Tim</span><span class="px-1 text-primary shrink-0">:</span><span class="text-on-surface truncate">{{ $item->ketuaTim->nama }}</span></div>
                            @endif
                            @if($item->anggota->count() > 0)
                                <div class="flex items-start">
                                    <span class="w-[90px] font-bold text-primary shrink-0">Anggota</span><span class="px-1 text-primary shrink-0">:</span>
                                    <span class="text-on-surface">
                                        <ul class="list-none m-0 p-0">
                                            @foreach($item->anggota as $anggota)
                                                <li class="truncate">{{ $loop->iteration }}. {{ $anggota->nama }}</li>
                                            @endforeach
                                        </ul>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-border-subtle mt-auto">
                            <a href="{{ route('pengawasan.show', $item->id) }}" class="flex-1 flex items-center justify-center gap-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 py-2 rounded-lg text-[12px] font-bold transition-colors">
                                <span class="material-symbols-outlined text-[16px]">visibility</span> Detail
                            </a>
                            <a href="{{ route('pengawasan.edit', $item->id) }}" class="flex-1 flex items-center justify-center gap-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 py-2 rounded-lg text-[12px] font-bold transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Ubah
                            </a>
                            <form action="{{ route('pengawasan.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 py-2 rounded-lg text-[12px] font-bold transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- LIST VIEW -->
        <div id="viewList" style="display:none;">
            <div class="flex flex-col gap-3">
                @foreach($pengawasan as $index => $item)
                    @php
                        $badgeClass = 'bg-blue-500/10 text-blue-700 border-blue-500/20';
                        if($item->jenis_penugasan == 'Reviu') $badgeClass = 'bg-amber-500/10 text-amber-700 border-amber-500/20';
                        elseif($item->jenis_penugasan == 'Monitoring') $badgeClass = 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20';
                        elseif($item->jenis_penugasan == 'Evaluasi') $badgeClass = 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20';
                        elseif($item->jenis_penugasan == 'Pendampingan') $badgeClass = 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20';
                        elseif($item->jenis_penugasan == 'Perjalanan Dinas Luar Daerah') $badgeClass = 'bg-pink-500/10 text-pink-700 border-pink-500/20';
                    @endphp
                    <div class="bg-surface-card border border-border-subtle rounded-xl p-4 flex flex-col md:flex-row items-start md:items-center gap-4 hover:shadow-sm transition-shadow">
                        <div class="w-10 h-10 bg-surface-container-high rounded-lg flex items-center justify-center text-[12px] font-bold text-primary shrink-0">
                            #{{ $pengawasan->firstItem() + $index }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-primary text-[14px] mb-1 truncate">{{ $item->nomor_st }}</div>
                            <div class="flex flex-wrap items-center gap-4 text-[12px] text-on-surface-variant mb-1">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $item->tanggal_st->format('d/m/Y') }}</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">timer</span> {{ $item->lama_penugasan }} Hari</span>
                                <span class="flex items-center gap-1 truncate"><span class="material-symbols-outlined text-[14px]">location_on</span> {{ $item->lokasi_penugasan }}</span>
                                @if($item->ketuaTim)
                                    <span class="flex items-center gap-1 truncate"><span class="material-symbols-outlined text-[14px]">person</span> {{ $item->ketuaTim->nama }}</span>
                                @endif
                            </div>
                            <div class="text-[12px] text-on-surface-variant truncate max-w-2xl">{{ $item->uraian_penugasan }}</div>
                        </div>
                        <div class="flex flex-col md:items-end gap-2 shrink-0">
                            <div class="flex gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badgeClass }} whitespace-nowrap">
                                    {{ $item->jenis_penugasan }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full text-white {{ $item->status == 'selesai' ? 'bg-green-600' : 'bg-rose-600' }} whitespace-nowrap">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 w-full md:w-auto">
                                <a href="{{ route('pengawasan.show', $item->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors" title="Detail">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                                <a href="{{ route('pengawasan.edit', $item->id) }}" class="w-8 h-8 rounded bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors" title="Ubah">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>
                                <form action="{{ route('pengawasan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($pengawasan->hasPages())
            <div class="mt-6 border-t border-border-subtle pt-4 flex justify-center">
                {{ $pengawasan->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <div class="bg-surface-card border border-border-subtle rounded-xl p-16 flex flex-col items-center justify-center text-center shadow-sm w-full">
            <span class="material-symbols-outlined text-[64px] text-on-surface-variant opacity-30 mb-4">search_off</span>
            <h3 class="font-bold text-label-lg text-primary mb-2">Tidak Ada Data Ditemukan</h3>
            <p class="text-[13px] text-on-surface-variant">Coba ubah filter atau kata kunci pencarian</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    const STORAGE_KEY = 'pengawasan_view';

    function setView(mode) {
        const card = document.getElementById('viewCard');
        const list = document.getElementById('viewList');
        const btnCard = document.getElementById('btnCard');
        const btnList = document.getElementById('btnList');

        if (mode === 'card') {
            if(card) card.style.display = 'block';
            if(list) list.style.display = 'none';
            btnCard.classList.add('bg-primary', 'text-white');
            btnCard.classList.remove('text-on-surface-variant', 'hover:bg-surface-container-high');
            btnList.classList.remove('bg-primary', 'text-white');
            btnList.classList.add('text-on-surface-variant', 'hover:bg-surface-container-high');
        } else {
            if(card) card.style.display = 'none';
            if(list) list.style.display = 'block';
            btnList.classList.add('bg-primary', 'text-white');
            btnList.classList.remove('text-on-surface-variant', 'hover:bg-surface-container-high');
            btnCard.classList.remove('bg-primary', 'text-white');
            btnCard.classList.add('text-on-surface-variant', 'hover:bg-surface-container-high');
        }
        localStorage.setItem(STORAGE_KEY, mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const saved = localStorage.getItem(STORAGE_KEY) || 'card';
        setView(saved);
    });

    let debounceTimer;
    function debounceSubmit() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    }
</script>
@endpush
