@extends('layouts.app')

@section('title', 'Detail Pengawasan')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('pengawasan.index') }}" class="hover:text-primary transition-colors">Data Pengawasan</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Detail Pengawasan</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Detail Surat Tugas Pengawasan</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Lihat detail informasi surat tugas pengawasan beserta personil yang terlibat.</p>
        </div>
        <a href="{{ route('pengawasan.index') }}" class="bg-surface-container-high text-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-surface-container-highest transition-colors focus:outline-none border border-outline-variant font-semibold text-[13px]">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Column -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            <!-- Informasi Surat Tugas -->
            <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">description</span>
                    <h2 class="font-bold text-label-lg text-primary">Informasi Surat Tugas</h2>
                </div>
                <div class="p-6 bg-white flex flex-col">
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Uraian</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">{{ $pengawasan->uraian_penugasan }}</div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Nomor ST</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">{{ $pengawasan->nomor_st }}</div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Tanggal ST</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">{{ $pengawasan->tanggal_st->format('d F Y') }}</div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Lama Penugasan</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">{{ $pengawasan->lama_penugasan }} Hari</div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Jenis</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">
                            @php
                                $badgeClass = 'bg-blue-100 text-blue-800';
                                if($pengawasan->jenis_penugasan == 'Reviu') $badgeClass = 'bg-amber-100 text-amber-800';
                                elseif($pengawasan->jenis_penugasan == 'Monitoring') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                elseif($pengawasan->jenis_penugasan == 'Evaluasi') $badgeClass = 'bg-indigo-100 text-indigo-800';
                                elseif($pengawasan->jenis_penugasan == 'Pendampingan') $badgeClass = 'bg-cyan-100 text-cyan-800';
                                elseif($pengawasan->jenis_penugasan == 'Perjalanan Dinas Luar Daerah') $badgeClass = 'bg-pink-100 text-pink-800';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold tracking-wide {{ $badgeClass }}">{{ $pengawasan->jenis_penugasan }}</span>
                        </div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Status</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">
                            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold tracking-wide {{ $pengawasan->status == 'selesai' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">{{ $pengawasan->status_label }}</span>
                        </div>
                    </div>
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">Lokasi</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">{{ $pengawasan->lokasi_penugasan }}</div>
                    </div>
                    @if($pengawasan->status === 'selesai' && $pengawasan->file_laporan)
                    <div class="flex py-3 border-b border-outline-variant/30 last:border-0 items-center">
                        <div class="w-[140px] shrink-0 text-[12px] font-bold text-primary uppercase tracking-wide">File Laporan</div>
                        <div class="flex-1 text-[13px] font-medium text-on-surface leading-relaxed">
                            <a href="{{ asset('storage/' . $pengawasan->file_laporan) }}" target="_blank" class="text-primary font-bold hover:underline flex items-center gap-1.5 w-max">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Download Laporan
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dasar Hukum -->
            @if($pengawasan->dasarHukum && $pengawasan->dasarHukum->count() > 0)
            <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">gavel</span>
                    <h2 class="font-bold text-label-lg text-primary">Dasar Hukum</h2>
                </div>
                <div class="p-6 bg-white flex flex-col gap-2">
                    @foreach($pengawasan->dasarHukum as $dasar)
                    <div class="flex gap-3 p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/20">
                        <div class="w-6 h-6 shrink-0 bg-primary text-white rounded-full flex items-center justify-center font-bold text-[11px]">{{ $loop->iteration }}</div>
                        <div class="flex-1 text-[13px] text-on-surface font-medium leading-relaxed">{{ $dasar->isi_dasar }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Personil Penugasan -->
            <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">group</span>
                    <h2 class="font-bold text-label-lg text-primary">Personil Penugasan</h2>
                </div>
                <div class="p-6 bg-white flex flex-col gap-2">
                    @if($pengawasan->penanggungJawab)
                        <div class="flex items-center gap-3 p-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg">
                            <div class="w-[90px] shrink-0 bg-primary text-white text-[10px] font-bold py-1 px-2 rounded text-center uppercase tracking-wider">PJ</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[13px] text-primary truncate">{{ $pengawasan->penanggungJawab->nama }}</div>
                                <div class="text-[11px] text-on-surface-variant truncate">{{ $pengawasan->penanggungJawab->jabatan }} &bull; {{ $pengawasan->penanggungJawab->golongan }}</div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ url('/pengawasan/' . $pengawasan->id . '/cetak-sppd/' . $pengawasan->penanggung_jawab_id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" target="_blank">
                                    <span class="material-symbols-outlined text-[14px]">print</span>
                                    SPPD
                                </a>
                                <button class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" onclick="openKwitansiModal({{ $pengawasan->penanggung_jawab_id }}, '{{ $pengawasan->penanggungJawab->nama }}', 'PJ')">
                                    <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                    Kwitansi
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($pengawasan->pengendaliTeknis)
                        <div class="flex items-center gap-3 p-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg">
                            <div class="w-[90px] shrink-0 bg-primary text-white text-[10px] font-bold py-1 px-2 rounded text-center uppercase tracking-wider">PT</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[13px] text-primary truncate">{{ $pengawasan->pengendaliTeknis->nama }}</div>
                                <div class="text-[11px] text-on-surface-variant truncate">{{ $pengawasan->pengendaliTeknis->jabatan }} &bull; {{ $pengawasan->pengendaliTeknis->golongan }}</div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ url('/pengawasan/' . $pengawasan->id . '/cetak-sppd/' . $pengawasan->pengendali_teknis_id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" target="_blank">
                                    <span class="material-symbols-outlined text-[14px]">print</span>
                                    SPPD
                                </a>
                                <button class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" onclick="openKwitansiModal({{ $pengawasan->pengendali_teknis_id }}, '{{ $pengawasan->pengendaliTeknis->nama }}', 'PT')">
                                    <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                    Kwitansi
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($pengawasan->ketuaTim)
                        <div class="flex items-center gap-3 p-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg">
                            <div class="w-[90px] shrink-0 bg-primary text-white text-[10px] font-bold py-1 px-2 rounded text-center uppercase tracking-wider">KETUA TIM</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[13px] text-primary truncate">{{ $pengawasan->ketuaTim->nama }}</div>
                                <div class="text-[11px] text-on-surface-variant truncate">{{ $pengawasan->ketuaTim->jabatan }} &bull; {{ $pengawasan->ketuaTim->golongan }}</div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ url('/pengawasan/' . $pengawasan->id . '/cetak-sppd/' . $pengawasan->ketua_tim_id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" target="_blank">
                                    <span class="material-symbols-outlined text-[14px]">print</span>
                                    SPPD
                                </a>
                                <button class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" onclick="openKwitansiModal({{ $pengawasan->ketua_tim_id }}, '{{ $pengawasan->ketuaTim->nama }}', 'KETUA TIM')">
                                    <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                    Kwitansi
                                </button>
                            </div>
                        </div>
                    @endif
                    @foreach($pengawasan->anggota as $anggota)
                        <div class="flex items-center gap-3 p-3 bg-surface-container-lowest border border-outline-variant/30 rounded-lg">
                            <div class="w-[90px] shrink-0 bg-primary text-white text-[10px] font-bold py-1 px-2 rounded text-center uppercase tracking-wider">ANGGOTA</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[13px] text-primary truncate">{{ $anggota->nama }}</div>
                                <div class="text-[11px] text-on-surface-variant truncate">{{ $anggota->jabatan }} &bull; {{ $anggota->golongan }}</div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ url('/pengawasan/' . $pengawasan->id . '/cetak-sppd/' . $anggota->id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" target="_blank">
                                    <span class="material-symbols-outlined text-[14px]">print</span>
                                    SPPD
                                </a>
                                <button class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold rounded flex items-center gap-1 transition-colors" onclick="openKwitansiModal({{ $anggota->id }}, '{{ $anggota->nama }}', 'ANGGOTA')">
                                    <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                    Kwitansi
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>

        <!-- Side Column -->
        <div class="flex flex-col gap-6">
            
            <!-- Ringkasan -->
            <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">analytics</span>
                    <h2 class="font-bold text-label-lg text-primary">Ringkasan</h2>
                </div>
                <div class="p-6 bg-white">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-lg text-center">
                            <div class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Durasi</div>
                            <div class="text-title-lg font-bold text-on-surface">{{ $pengawasan->lama_penugasan }}</div>
                        </div>
                        <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-lg text-center">
                            <div class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Tim</div>
                            <div class="text-title-lg font-bold text-on-surface">{{ 3 + $pengawasan->anggota->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksi -->
            <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full">
                <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[24px]">flash_on</span>
                    <h2 class="font-bold text-label-lg text-primary">Aksi</h2>
                </div>
                <div class="p-6 bg-white flex flex-col gap-3">
                    <a href="{{ route('pengawasan.cetak-surat-tugas', $pengawasan->id) }}" class="w-full h-10 rounded-lg font-bold text-[13px] bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm flex items-center justify-center gap-2" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Cetak Surat Tugas
                    </a>
                    <a href="{{ route('pengawasan.edit', $pengawasan->id) }}" class="w-full h-10 rounded-lg font-bold text-[13px] bg-amber-500 text-white hover:bg-amber-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit Data
                    </a>
                    <form action="{{ route('pengawasan.destroy', $pengawasan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pengawasan {{ $pengawasan->nomor_st }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full h-10 rounded-lg font-bold text-[13px] bg-rose-600 text-white hover:bg-rose-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Kwitansi -->
<div class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" id="modalKwitansi">
    <div class="bg-surface-card rounded-xl max-w-[500px] w-full max-h-[90vh] flex flex-col shadow-xl border border-border-subtle">
        <div class="p-5 border-b border-border-subtle flex justify-between items-center bg-surface-container-lowest rounded-t-xl">
            <h3 class="font-bold text-label-lg text-primary flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                Cetak Kwitansi
            </h3>
            <button type="button" class="text-on-surface-variant hover:text-primary transition-colors focus:outline-none" onclick="closeKwitansiModal()">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto bg-white">
            <div class="bg-surface-container-lowest p-4 rounded-lg mb-5 border border-outline-variant/30 flex flex-col gap-2">
                <div class="flex justify-between items-start text-[13px]">
                    <span class="text-on-surface-variant font-medium">Nama Pegawai:</span>
                    <span class="text-on-surface font-bold text-right ml-2" id="kwitansiNama">-</span>
                </div>
                <div class="flex justify-between items-start text-[13px]">
                    <span class="text-on-surface-variant font-medium">Jabatan:</span>
                    <span class="text-on-surface font-bold text-right ml-2" id="kwitansiJabatan">-</span>
                </div>
                <div class="flex justify-between items-start text-[13px]">
                    <span class="text-on-surface-variant font-medium">Uraian Penugasan:</span>
                    <span class="text-on-surface font-bold text-right ml-2">{{ $pengawasan->uraian_penugasan }}</span>
                </div>
                <div class="flex justify-between items-start text-[13px]">
                    <span class="text-on-surface-variant font-medium">Lokasi:</span>
                    <span class="text-on-surface font-bold text-right ml-2">{{ $pengawasan->lokasi_penugasan }}</span>
                </div>
                <div class="flex justify-between items-start text-[13px]">
                    <span class="text-on-surface-variant font-medium">Lama Penugasan:</span>
                    <span class="text-on-surface font-bold text-right ml-2">{{ $pengawasan->lama_penugasan }} Hari</span>
                </div>
            </div>

            <form id="formKwitansi" action="" method="GET" target="_blank" class="flex flex-col gap-2">
                <label class="text-[13px] font-bold text-on-surface">Nominal Kwitansi <span class="text-error">*</span></label>
                <input
                    type="number"
                    name="nominal"
                    id="nominalKwitansi"
                    class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors"
                    placeholder="Masukkan nominal (contoh: 500000)"
                    required
                    min="0"
                    step="1000"
                >
                <div class="text-[11px] text-on-surface-variant">
                    Masukkan nominal dalam angka tanpa titik atau koma
                </div>
            </form>
        </div>
        <div class="p-4 border-t border-border-subtle bg-surface-container-lowest rounded-b-xl flex justify-end gap-3">
            <button type="button" class="px-5 py-2.5 rounded-lg font-bold text-[13px] text-on-surface-variant hover:bg-surface-container-high transition-colors" onclick="closeKwitansiModal()">
                Batal
            </button>
            <button type="button" class="px-5 py-2.5 rounded-lg font-bold text-[13px] bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm" onclick="submitKwitansi()">
                Cetak Kwitansi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPegawaiId = null;

    function openKwitansiModal(pegawaiId, namaPegawai, jabatan) {
        currentPegawaiId = pegawaiId;
        document.getElementById('kwitansiNama').textContent = namaPegawai;
        document.getElementById('kwitansiJabatan').textContent = jabatan;
        document.getElementById('nominalKwitansi').value = '';
        
        const modal = document.getElementById('modalKwitansi');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeKwitansiModal() {
        const modal = document.getElementById('modalKwitansi');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentPegawaiId = null;
    }

    function submitKwitansi() {
        const nominal = document.getElementById('nominalKwitansi').value;

        if (!nominal || nominal <= 0) {
            alert('Mohon masukkan nominal yang valid!');
            return;
        }

        if (!currentPegawaiId) {
            alert('Terjadi kesalahan! Silakan coba lagi.');
            return;
        }

        // Redirect to cetak kwitansi route
        const url = `/pengawasan/{{ $pengawasan->id }}/cetak-kwitansi/${currentPegawaiId}?nominal=${nominal}`;
        window.open(url, '_blank');

        // Close modal
        closeKwitansiModal();
    }

    // Close modal when clicking outside
    document.getElementById('modalKwitansi').addEventListener('click', function(e) {
        if (e.target === this) {
            closeKwitansiModal();
        }
    });

    // Handle Enter key in form
    document.getElementById('nominalKwitansi').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitKwitansi();
        }
    });
</script>
@endpush
