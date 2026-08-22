@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(15px);} to { opacity:1; transform:translateY(0);} }
.kpi-card { animation: fadeUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) both; transition: all 0.3s ease; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.06); border-color: rgba(37, 99, 235, 0.2); }
.quick-link:hover .ql-icon { transform: scale(1.15) rotate(3deg); }
.ql-icon { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endpush

@section('content')

@php
    $totalPengawasan = \App\Models\Pengawasan::count();
    $pengawasanBulanIni = \App\Models\Pengawasan::whereMonth('tanggal_st', now()->month)->count();
    $totalLaporanSelesai = \App\Models\Pengawasan::whereNotNull('file_laporan')->count();
    $recentPengawasan = \App\Models\Pengawasan::orderBy('tanggal_st', 'desc')->take(5)->get();
@endphp

<!-- Header Section -->
<div class="flex items-end justify-between flex-wrap gap-4 mb-8">
    <div class="animate-fadeInUp">
        <div class="flex items-center text-label-sm text-on-surface-variant gap-2 mb-2 font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px] text-on-surface-variant">home</span>
            <span>Inspektorat</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface">Dashboard</span>
        </div>
        <h1 class="text-headline-lg font-extrabold text-on-surface leading-tight tracking-tight">Selamat Datang, <span>{{ Auth::user()->name }}</span> 👋</h1>
        <p class="text-body-md text-on-surface-variant mt-2 font-medium">Sistem Informasi Manajemen Penugasan APIP - Kab. Puncak Jaya</p>
    </div>
    <div class="flex items-center gap-3 text-label-sm font-bold text-on-surface bg-surface shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-border-subtle rounded-xl px-5 py-3 shrink-0 animate-fadeInUp" style="animation-delay: 0.1s">
        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
        </div>
        <span id="today-date" class="tracking-wide">Memuat...</span>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 w-full mb-8">
    <!-- Pegawai -->
    <div class="kpi-card bg-surface border border-border-subtle rounded-2xl p-6 flex flex-col gap-4 shadow-[0_4px_16px_rgba(0,0,0,0.02)] relative overflow-hidden group" style="animation-delay:0.1s">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
        <div class="flex justify-between items-start relative z-10">
            <span class="text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider">Total Pegawai</span>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 text-blue-600 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1">groups</span>
            </div>
        </div>
        <div class="relative z-10 mt-2">
            <div class="text-4xl font-black text-on-surface leading-none tracking-tight">{{ $totalPegawai }}</div>
            <p class="text-body-sm font-medium text-on-surface-variant mt-3 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Seluruh APIP terdaftar
            </p>
        </div>
    </div>

    <!-- Surat Tugas -->
    <div class="kpi-card bg-surface border border-border-subtle rounded-2xl p-6 flex flex-col gap-4 shadow-[0_4px_16px_rgba(0,0,0,0.02)] relative overflow-hidden group" style="animation-delay:0.15s">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-colors"></div>
        <div class="flex justify-between items-start relative z-10">
            <span class="text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider">Surat Tugas</span>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-indigo-600/10 text-indigo-600 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1">assignment</span>
            </div>
        </div>
        <div class="relative z-10 mt-2">
            <div class="text-4xl font-black text-on-surface leading-none tracking-tight">{{ $totalPengawasan }}</div>
            <p class="text-body-sm font-medium text-on-surface-variant mt-3 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Total penugasan berjalan
            </p>
        </div>
    </div>

    <!-- Laporan Selesai -->
    <div class="kpi-card bg-surface border border-border-subtle rounded-2xl p-6 flex flex-col gap-4 shadow-[0_4px_16px_rgba(0,0,0,0.02)] relative overflow-hidden group" style="animation-delay:0.2s">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
        <div class="flex justify-between items-start relative z-10">
            <span class="text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider">Laporan Selesai</span>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 text-emerald-600 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1">task_alt</span>
            </div>
        </div>
        <div class="relative z-10 mt-2">
            <div class="text-4xl font-black text-on-surface leading-none tracking-tight">{{ $totalLaporanSelesai }}</div>
            <p class="text-body-sm font-medium text-on-surface-variant mt-3 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LHKP / LHP diterbitkan
            </p>
        </div>
    </div>

    <!-- Tugas Bulan Ini -->
    <div class="kpi-card bg-surface border border-border-subtle rounded-2xl p-6 flex flex-col gap-4 shadow-[0_4px_16px_rgba(0,0,0,0.02)] relative overflow-hidden group" style="animation-delay:0.25s">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
        <div class="flex justify-between items-start relative z-10">
            <span class="text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider">Tugas Bulan Ini</span>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 text-amber-600 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1">event_available</span>
            </div>
        </div>
        <div class="relative z-10 mt-2">
            <div class="text-4xl font-black text-on-surface leading-none tracking-tight">{{ $pengawasanBulanIni }}</div>
            <p class="text-body-sm font-medium text-on-surface-variant mt-3 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Penugasan bulan {{ now()->translatedFormat('F') }}
            </p>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full items-start animate-fadeInUp" style="animation-delay:0.3s">
    
    <!-- Quick Links -->
    <div class="lg:col-span-4 flex flex-col w-full h-full">
        <div class="bg-surface border border-border-subtle rounded-2xl overflow-hidden shadow-[0_4px_16px_rgba(0,0,0,0.02)] w-full p-6 h-full flex flex-col">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                </div>
                <h2 class="text-headline-md font-bold text-on-surface tracking-tight">Akses Cepat</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 flex-1">
                @php
                    $quickLinks = [
                        ['route' => 'pengawasan.create', 'icon' => 'add_circle',        'label' => 'Buat Tugas Baru', 'color' => 'text-primary',   'bg' => 'bg-primary/10 hover:bg-primary hover:text-white', 'icon_color' => 'text-primary group-hover:text-white'],
                        ['route' => 'pegawai.index',     'icon' => 'groups',            'label' => 'Data Pegawai',  'color' => 'text-blue-600',   'bg' => 'bg-blue-50/50 hover:bg-blue-500 hover:text-white border border-blue-100', 'icon_color' => 'text-blue-600 group-hover:text-white'],
                        ['route' => 'laporan.index',     'icon' => 'analytics',         'label' => 'Arsip Laporan', 'color' => 'text-teal-600',   'bg' => 'bg-teal-50/50 hover:bg-teal-500 hover:text-white border border-teal-100', 'icon_color' => 'text-teal-600 group-hover:text-white'],
                        ['route' => 'rekap.index',       'icon' => 'table_chart',       'label' => 'Rekap Data',    'color' => 'text-violet-600', 'bg' => 'bg-violet-50/50 hover:bg-violet-500 hover:text-white border border-violet-100', 'icon_color' => 'text-violet-600 group-hover:text-white'],
                    ];
                @endphp
                @foreach($quickLinks as $ql)
                <a href="{{ route($ql['route']) }}" class="quick-link group flex flex-col items-center justify-center gap-3 p-5 rounded-xl {{ $ql['bg'] }} transition-all duration-300 shadow-sm">
                    <div class="ql-icon w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center {{ $ql['icon_color'] }} transition-colors">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1">{{ $ql['icon'] }}</span>
                    </div>
                    <span class="text-label-md font-bold tracking-wide text-center transition-colors">{{ $ql['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="lg:col-span-8 flex flex-col w-full h-full">
        <div class="bg-surface border border-border-subtle rounded-2xl overflow-hidden shadow-[0_4px_16px_rgba(0,0,0,0.02)] w-full p-0 flex flex-col h-full">
            <div class="p-6 border-b border-border-subtle flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                    </div>
                    <h2 class="text-headline-md font-bold text-on-surface tracking-tight">Penugasan Terbaru</h2>
                </div>
                <a href="{{ route('pengawasan.index') }}" class="text-label-sm font-bold text-primary hover:text-indigo-700 flex items-center gap-1 transition-colors">
                    Lihat Semua <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50">
                            <th class="px-6 py-4 text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider border-b border-border-subtle">No. Surat Tugas</th>
                            <th class="px-6 py-4 text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider border-b border-border-subtle">Tanggal</th>
                            <th class="px-6 py-4 text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider border-b border-border-subtle">Jenis Penugasan</th>
                            <th class="px-6 py-4 text-label-sm font-extrabold text-on-surface-variant uppercase tracking-wider border-b border-border-subtle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse($recentPengawasan as $p)
                        <tr class="hover:bg-surface-container-low/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-on-surface">{{ $p->nomor_st }}</div>
                                <div class="text-body-sm text-on-surface-variant mt-1 font-medium truncate max-w-[250px]" title="{{ $p->obyek_pengawasan ?? 'Tidak ada obyek' }}">{{ $p->obyek_pengawasan ?? 'Tidak ada obyek' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-surface-container font-semibold text-body-sm text-on-surface">
                                    <span class="material-symbols-outlined text-[14px] text-on-surface-variant">calendar_month</span>
                                    {{ \Carbon\Carbon::parse($p->tanggal_st)->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-label-sm font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                                    {{ $p->jenis_penugasan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('pengawasan.edit', $p->id) }}" class="w-8 h-8 rounded-lg bg-surface-container hover:bg-primary/10 hover:text-primary flex items-center justify-center transition-colors text-on-surface-variant" title="Detail">
                                    <span class="material-symbols-outlined text-[18px]">edit_document</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-[32px] text-on-surface-variant/50">inbox</span>
                                    </div>
                                    <p class="font-bold">Belum ada data penugasan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Calendar Section -->
<div class="w-full mt-6 animate-fadeInUp" style="animation-delay:0.4s">
    <div class="bg-surface border border-border-subtle rounded-2xl overflow-hidden shadow-[0_4px_16px_rgba(0,0,0,0.02)] w-full p-6 flex flex-col">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
            </div>
            <h2 class="text-headline-md font-bold text-on-surface tracking-tight">Jadwal Penugasan</h2>
        </div>
        
        <div id="calendar" class="w-full min-h-[500px]"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<!-- Tippy.js for beautiful tooltips -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css"/>

<script>
    const d = new Date();
    document.getElementById('today-date').textContent = d.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'standard',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu'
            },
            events: '/api/kalender-data',
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            },
            eventDidMount: function(info) {
                // Remove native title to prevent double tooltips
                info.el.removeAttribute('title');
                
                // Create elegant HTML tooltip content
                let st = info.event.title;
                let uraian = info.event.extendedProps.uraian || '-';
                let status = info.event.extendedProps.status_label || '';
                let statusColor = info.event.classNames.includes('event-selesai') ? 'text-emerald-400' : 'text-indigo-400';
                
                let tooltipContent = `
                    <div class="text-left p-1">
                        <div class="font-bold text-sm mb-1">${st}</div>
                        <div class="text-xs text-gray-300 mb-2 leading-relaxed">${uraian}</div>
                        <div class="text-xs font-semibold ${statusColor}">${status}</div>
                    </div>
                `;

                // Initialize Tippy.js
                tippy(info.el, {
                    content: tooltipContent,
                    allowHTML: true,
                    animation: 'scale',
                    theme: 'translucent',
                    placement: 'top',
                    arrow: true,
                    delay: [50, 0]
                });
            },
            height: 'auto',
            aspectRatio: 2.2,
        });
        calendar.render();
    });
</script>
<style>
    /* FullCalendar customizations */
    .fc {
        --fc-border-color: rgba(0, 0, 0, 0.06);
        --fc-button-text-color: #fff;
        --fc-button-bg-color: #4f46e5;
        --fc-button-border-color: #4f46e5;
        --fc-button-hover-bg-color: #4338ca;
        --fc-button-hover-border-color: #4338ca;
        --fc-button-active-bg-color: #3730a3;
        --fc-button-active-border-color: #3730a3;
        --fc-today-bg-color: rgba(79, 70, 229, 0.03);
        font-family: inherit;
    }
    .fc .fc-toolbar-title {
        font-weight: 800;
        font-size: 1.125rem;
        color: #1e293b;
    }
    .fc .fc-button {
        border-radius: 0.5rem;
        padding: 0.4rem 1rem;
        font-weight: 600;
        text-transform: capitalize;
        transition: all 0.2s;
        font-size: 0.875rem;
    }
    .fc-theme-standard th {
        padding: 0.875rem 0;
        background-color: #ffffff;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
    }
    .fc-daygrid-day-number {
        font-weight: 600;
        color: #64748b;
        padding: 8px !important;
        font-size: 0.875rem;
    }
    
    /* Minimalist Event Markings (Ribbons) */
    .fc-event {
        border-radius: 4px !important;
        height: 6px !important; /* Make it a thin ribbon */
        margin: 2px 6px !important; /* Space from edges */
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none !important;
        overflow: hidden;
    }
    .fc-event:hover {
        transform: scaleY(1.5);
        z-index: 10 !important;
    }
    /* Hide the text inside events completely */
    .fc-event-main, .fc-event-title, .fc-event-time {
        display: none !important;
    }
    .fc-daygrid-event-dot {
        display: none !important;
    }
    
    /* Colors */
    .event-selesai {
        background: #10b981 !important; /* Solid emerald */
    }
    .event-belum-selesai {
        background: #6366f1 !important; /* Solid indigo */
    }

    /* Tippy Theme Customization */
    .tippy-box[data-theme~='translucent'] {
        background-color: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(8px);
        color: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .tippy-box[data-theme~='translucent'][data-placement^='top'] > .tippy-arrow::before {
        border-top-color: rgba(15, 23, 42, 0.95);
    }
</style>
@endpush
