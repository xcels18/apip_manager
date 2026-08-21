@extends('layouts.app')

@section('title', 'Edit Pengawasan')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('pengawasan.index') }}" class="hover:text-primary transition-colors">Data Pengawasan</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Edit Pengawasan</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Edit Surat Tugas Pengawasan</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Perbarui detail surat tugas pengawasan, monitoring, atau reviu.</p>
        </div>
        <a href="{{ route('pengawasan.index') }}" class="bg-surface-container-high text-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-surface-container-highest transition-colors focus:outline-none border border-outline-variant font-semibold text-[13px]">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop">

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 px-4 py-3 rounded-lg flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined">error</span>
            <span class="text-[13px] font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full max-w-5xl">
        <form action="{{ route('pengawasan.update', $pengawasan->id) }}" method="POST" enctype="multipart/form-data" id="editPengawasanForm">
            @csrf
            @method('PUT')
            
            <!-- Section 1: Informasi Surat Tugas -->
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">description</span>
                <h2 class="font-bold text-label-lg text-primary">Informasi Surat Tugas</h2>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-border-subtle bg-white">
                <div class="flex flex-col gap-2">
                    <label class="block text-[13px] font-bold text-on-surface">Nomor ST</label>
                    <div class="flex items-stretch h-10">
                        <span class="flex items-center px-3 bg-surface-container-low border border-r-0 border-outline-variant rounded-l-lg text-[12px] text-on-surface-variant font-bold whitespace-nowrap">100.3.5.4/</span>
                        <input
                            type="text"
                            name="nomor_st_number"
                            class="w-20 px-3 border-y border-outline-variant {{ $errors->has('nomor_st_number') ? 'border-error' : 'focus:ring-1 focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none transition-colors"
                            value="{{ old('nomor_st_number', $nomorStNumber) }}"
                            placeholder="001"
                        >
                        <span class="flex items-center px-3 bg-surface-container-low border border-l-0 border-outline-variant rounded-r-lg text-[12px] text-on-surface-variant font-bold whitespace-nowrap">/ST/ITKAB/{{ $tahun }}</span>
                    </div>
                    @error('nomor_st_number')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="tanggal_st" class="block text-[13px] font-bold text-on-surface">Tanggal ST <span class="text-error">*</span></label>
                    <input
                        type="date"
                        id="tanggal_st"
                        name="tanggal_st"
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('tanggal_st') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors"
                        value="{{ old('tanggal_st', $pengawasan->tanggal_st->format('Y-m-d')) }}"
                        required
                    >
                    @error('tanggal_st')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="lama_penugasan" class="block text-[13px] font-bold text-on-surface">Lama Penugasan <span class="text-error">*</span></label>
                    <div class="relative flex items-center h-10">
                        <input
                            type="number"
                            id="lama_penugasan"
                            name="lama_penugasan"
                            class="w-full h-full px-3 pr-12 rounded-lg border {{ $errors->has('lama_penugasan') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors"
                            value="{{ old('lama_penugasan', $pengawasan->lama_penugasan) }}"
                            min="1"
                            placeholder="5"
                            required
                        >
                        <span class="absolute right-3 text-[12px] font-bold text-on-surface-variant pointer-events-none">hari</span>
                    </div>
                    @error('lama_penugasan')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <label for="jenis_penugasan" class="block text-[13px] font-bold text-on-surface">Jenis Penugasan <span class="text-error">*</span></label>
                    <div class="relative">
                        <select
                            id="jenis_penugasan"
                            name="jenis_penugasan"
                            class="w-full h-10 px-3 pr-8 rounded-lg border {{ $errors->has('jenis_penugasan') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest appearance-none cursor-pointer text-on-surface focus:outline-none focus:ring-1 transition-colors"
                            required
                        >
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Audit" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Audit' ? 'selected' : '' }}>Audit</option>
                            <option value="Reviu" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Reviu' ? 'selected' : '' }}>Reviu</option>
                            <option value="Monitoring" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="Evaluasi" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Evaluasi' ? 'selected' : '' }}>Evaluasi</option>
                            <option value="Pendampingan" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Pendampingan' ? 'selected' : '' }}>Pendampingan</option>
                            <option value="Perjalanan Dinas Luar Daerah" {{ old('jenis_penugasan', $pengawasan->jenis_penugasan) == 'Perjalanan Dinas Luar Daerah' ? 'selected' : '' }}>Perjalanan Dinas Luar Daerah</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
                    </div>
                    @error('jenis_penugasan')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="alat_angkut" class="block text-[13px] font-bold text-on-surface">Alat Angkut <span class="text-error">*</span></label>
                    <div class="relative">
                        <select
                            id="alat_angkut"
                            name="alat_angkut"
                            class="w-full h-10 px-3 pr-8 rounded-lg border {{ $errors->has('alat_angkut') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest appearance-none cursor-pointer text-on-surface focus:outline-none focus:ring-1 transition-colors"
                            required
                        >
                            <option value="">-- Pilih Alat Angkut --</option>
                            <option value="darat" {{ old('alat_angkut', $pengawasan->alat_angkut) == 'darat' ? 'selected' : '' }}>Transportasi Darat</option>
                            <option value="udara" {{ old('alat_angkut', $pengawasan->alat_angkut) == 'udara' ? 'selected' : '' }}>Transportasi Udara</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
                    </div>
                    @error('alat_angkut')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Section 2: Status & File Laporan -->
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">task_alt</span>
                <h2 class="font-bold text-label-lg text-primary">Status & Laporan</h2>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-border-subtle bg-white">
                <div class="flex flex-col gap-2">
                    <label for="status" class="block text-[13px] font-bold text-on-surface">Status <span class="text-error">*</span></label>
                    <div class="relative">
                        <select
                            id="status"
                            name="status"
                            class="w-full h-10 px-3 pr-8 rounded-lg border {{ $errors->has('status') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest appearance-none cursor-pointer text-on-surface focus:outline-none focus:ring-1 transition-colors"
                            required
                        >
                            <option value="belum_selesai" {{ old('status', $pengawasan->status) == 'belum_selesai' ? 'selected' : '' }}>Belum Selesai</option>
                            <option value="selesai" {{ old('status', $pengawasan->status) == 'selesai' ? 'selected' : '' }} id="statusSelesaiOption">Selesai</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline-variant text-[18px]">expand_more</span>
                    </div>
                    @error('status')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                    <div id="statusHelpText" style="display: none;" class="mt-1 flex items-center gap-1 text-[11px] font-medium text-amber-600">
                        <span class="material-symbols-outlined text-[14px]">warning</span> Status hanya bisa diubah menjadi "Selesai" jika sudah mengupload file laporan
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="file_laporan" class="block text-[13px] font-bold text-on-surface">File Laporan (PDF)</label>
                    <input
                        type="file"
                        id="file_laporan"
                        name="file_laporan"
                        class="w-full text-body-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[13px] file:font-bold file:bg-surface-container-high file:text-primary hover:file:bg-surface-container-highest file:cursor-pointer transition-colors border border-outline-variant rounded-lg bg-surface-container-lowest {{ $errors->has('file_laporan') ? 'border-error' : '' }}"
                        accept=".pdf"
                    >
                    @if($pengawasan->file_laporan)
                        <div class="text-[12px] text-on-surface-variant flex items-center gap-1 mt-1">
                            File saat ini:
                            <a href="{{ asset('storage/' . $pengawasan->file_laporan) }}" target="_blank" class="text-primary font-bold hover:underline">
                                {{ basename($pengawasan->file_laporan) }}
                            </a>
                        </div>
                    @endif
                    @error('file_laporan')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                    <div class="text-[11px] text-on-surface-variant mt-1">Upload file laporan dalam format PDF (max 10MB)</div>
                </div>
            </div>

            <!-- Section 3: Detail Penugasan -->
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">assignment</span>
                <h2 class="font-bold text-label-lg text-primary">Detail Penugasan</h2>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-border-subtle bg-white">
                <div class="flex flex-col gap-2">
                    <label for="uraian_penugasan" class="block text-[13px] font-bold text-on-surface">Uraian Penugasan <span class="text-error">*</span></label>
                    <textarea
                        id="uraian_penugasan"
                        name="uraian_penugasan"
                        class="w-full p-3 rounded-lg border {{ $errors->has('uraian_penugasan') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors min-h-[120px] resize-y"
                        placeholder="Masukkan uraian penugasan secara detail"
                        required
                    >{{ old('uraian_penugasan', $pengawasan->uraian_penugasan) }}</textarea>
                    @error('uraian_penugasan')
                        <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="lokasi_penugasan" class="block text-[13px] font-bold text-on-surface">Lokasi Penugasan <span class="text-error">*</span></label>
                        <input
                            type="text"
                            id="lokasi_penugasan"
                            name="lokasi_penugasan"
                            class="w-full h-10 px-3 rounded-lg border {{ $errors->has('lokasi_penugasan') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors"
                            value="{{ old('lokasi_penugasan', $pengawasan->lokasi_penugasan) }}"
                            placeholder="Contoh: Kantor Dinas Pendidikan"
                            required
                        >
                        @error('lokasi_penugasan')
                            <div class="text-error text-[11px] font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="block text-[13px] font-bold text-on-surface">Dasar Hukum <span class="text-error">*</span></label>
                        <div id="dasar-hukum-container" class="flex flex-col gap-2">
                            @if($pengawasan->dasarHukum->count() > 0)
                                @foreach($pengawasan->dasarHukum as $dasar)
                                    <div class="dasar-hukum-item flex gap-2 items-start relative group">
                                        <textarea
                                            name="dasar_hukum[]"
                                            class="flex-1 p-3 rounded-lg border {{ $errors->has('dasar_hukum.'.$loop->index) ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors min-h-[60px]"
                                            placeholder="Masukkan dasar hukum"
                                            required
                                        >{{ old('dasar_hukum.' . $loop->index, $dasar->isi_dasar) }}</textarea>
                                        @if($loop->index > 0)
                                            <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors shrink-0 mt-1" onclick="removeDasarHukum(this)" title="Hapus">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        @endif
                                        @error('dasar_hukum.'.$loop->index)
                                            <div class="text-error text-[11px] font-medium absolute -bottom-4 left-0">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            @else
                                <div class="dasar-hukum-item flex gap-2 items-start relative group">
                                    <textarea
                                        name="dasar_hukum[]"
                                        class="flex-1 p-3 rounded-lg border {{ $errors->has('dasar_hukum.0') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors min-h-[60px]"
                                        placeholder="Masukkan dasar hukum"
                                        required
                                    ></textarea>
                                    @error('dasar_hukum.0')
                                        <div class="text-error text-[11px] font-medium absolute -bottom-4 left-0">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        @error('dasar_hukum')
                            <div class="text-error text-[11px] font-medium mt-1">{{ $message }}</div>
                        @enderror
                        <button type="button" class="mt-2 text-[12px] font-bold text-primary flex items-center gap-1 hover:text-primary/80 transition-colors w-max" onclick="addDasarHukum()">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span> Tambah Dasar Hukum
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 4: Personil -->
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">group</span>
                <h2 class="font-bold text-label-lg text-primary">Personil Penugasan</h2>
            </div>

            <div class="p-6 border-b border-border-subtle bg-white">
                <!-- Hidden inputs for form submission -->
                <input type="hidden" id="penanggung_jawab_id" name="penanggung_jawab_id" value="{{ old('penanggung_jawab_id', $pengawasan->penanggung_jawab_id) }}" required>
                <input type="hidden" id="pengendali_teknis_id" name="pengendali_teknis_id" value="{{ old('pengendali_teknis_id', $pengawasan->pengendali_teknis_id) }}">
                <input type="hidden" id="ketua_tim_id" name="ketua_tim_id" value="{{ old('ketua_tim_id', $pengawasan->ketua_tim_id) }}" required>
                <div id="anggotaHiddenInputs">
                    @foreach($pengawasan->anggota as $anggota)
                        <input type="hidden" name="anggota[]" value="{{ $anggota->id }}">
                    @endforeach
                </div>

                <!-- Personil Display List (Will be populated by JS) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="personilListEdit">
                    <!-- Javascript will render items here -->
                </div>
            </div>

            <!-- Section 5: Penandatangan -->
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">draw</span>
                <h2 class="font-bold text-label-lg text-primary">Penandatangan Surat Tugas</h2>
            </div>

            <div class="p-6 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <label class="block text-[13px] font-bold text-on-surface">Jenis Penandatangan <span class="text-error">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-[13px] text-on-surface hover:text-primary transition-colors">
                                <input type="radio" name="penandatangan_type" value="definitif"
                                    id="penandatangan_definitif"
                                    {{ old('penandatangan_type', $pengawasan->penandatangan_type ?? 'definitif') == 'definitif' ? 'checked' : '' }}
                                    onchange="togglePenandatangan()"
                                    class="w-4 h-4 text-primary focus:ring-primary border-outline-variant">
                                Pejabat Definitif
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-[13px] text-on-surface hover:text-primary transition-colors">
                                <input type="radio" name="penandatangan_type" value="plh"
                                    id="penandatangan_plh"
                                    {{ old('penandatangan_type', $pengawasan->penandatangan_type ?? 'definitif') == 'plh' ? 'checked' : '' }}
                                    onchange="togglePenandatangan()"
                                    class="w-4 h-4 text-primary focus:ring-primary border-outline-variant">
                                Plh. (Pelaksana Harian)
                            </label>
                        </div>
                        @error('penandatangan_type')
                            <div class="text-error text-[11px] font-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2" id="penandatangan_definitif_info" style="{{ old('penandatangan_type', $pengawasan->penandatangan_type ?? 'definitif') == 'definitif' ? '' : 'display:none;' }}">
                        <label class="block text-[13px] font-bold text-on-surface">Penandatangan</label>
                        <div class="px-4 py-2 rounded-lg bg-primary/5 border border-primary/20 flex flex-col justify-center">
                            @php
                                $definitifNama = \App\Models\SystemSetting::where('key', 'definitif_nama')->first()->value ?? 'BOTTEN TANDIPADA';
                                $definitifJabatan = \App\Models\SystemSetting::where('key', 'definitif_jabatan')->first()->value ?? 'Plt. Inspektur';
                            @endphp
                            <span class="text-primary font-bold">{{ $definitifNama }}</span>
                            <span class="text-[11px] text-primary/80 uppercase">{{ $definitifJabatan }}</span>
                        </div>
                    </div>
                </div>

                <div id="penandatangan_plh_fields" class="mt-4" style="{{ old('penandatangan_type', $pengawasan->penandatangan_type ?? 'definitif') == 'plh' ? '' : 'display:none;' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl border border-border-subtle bg-surface-container-lowest">
                        <div class="flex flex-col gap-2">
                            <label class="block text-[13px] font-bold text-on-surface">Nama Plh. <span class="text-error">*</span></label>
                            <input type="hidden" id="penandatangan_plh_nama" name="penandatangan_plh_nama" value="{{ old('penandatangan_plh_nama', $pengawasan->penandatangan_plh_nama) }}">
                            <div class="relative">
                                <input
                                    type="text"
                                    id="penandatangan_plh_search"
                                    class="w-full h-10 px-3 rounded-lg border {{ $errors->has('penandatangan_plh_nama') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-white focus:outline-none focus:ring-1 transition-colors"
                                    value="{{ old('penandatangan_plh_nama', $pengawasan->penandatangan_plh_nama) }}"
                                    placeholder="Cari nama pegawai..."
                                    autocomplete="off"
                                    oninput="filterPlhPegawai(this.value)"
                                    onfocus="showPlhDropdown()"
                                >
                                <div id="plh_dropdown" style="display:none;" class="absolute top-full left-0 w-full mt-1 bg-white border border-border-subtle rounded-lg shadow-lg max-h-[220px] overflow-y-auto z-50 py-1"></div>
                            </div>
                            @error('penandatangan_plh_nama')
                                <div class="text-error text-[11px] font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="penandatangan_plh_jabatan" class="block text-[13px] font-bold text-on-surface">Jabatan Plh. <span class="text-error">*</span></label>
                            <input
                                type="text"
                                id="penandatangan_plh_jabatan"
                                name="penandatangan_plh_jabatan"
                                class="w-full h-10 px-3 rounded-lg border {{ $errors->has('penandatangan_plh_jabatan') ? 'border-error focus:ring-error' : 'border-outline-variant focus:ring-primary' }} text-body-sm bg-white focus:outline-none focus:ring-1 transition-colors"
                                value="{{ old('penandatangan_plh_jabatan', $pengawasan->penandatangan_plh_jabatan) }}"
                                placeholder="Otomatis terisi dari data pegawai"
                            >
                            @error('penandatangan_plh_jabatan')
                                <div class="text-error text-[11px] font-medium">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-surface-container-low border-t border-border-subtle flex justify-end items-center gap-3">
                <a href="{{ route('pengawasan.index') }}" class="px-5 py-2.5 rounded-lg font-bold text-[13px] text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg font-bold text-[13px] bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pencarian Personil untuk Anggota -->
<div class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" id="modalPersonil">
    <div class="bg-surface-card rounded-xl max-w-[600px] w-full max-h-[85vh] flex flex-col shadow-xl border border-border-subtle">
        <div class="p-5 border-b border-border-subtle flex justify-between items-center bg-surface-container-lowest rounded-t-xl">
            <h3 class="font-bold text-label-lg text-primary flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">group_add</span>
                Pilih Personil Anggota
            </h3>
            <button type="button" class="text-on-surface-variant hover:text-primary transition-colors focus:outline-none" onclick="closePersonilModal()">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>
        <div class="p-4 border-b border-border-subtle">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
                <input
                    type="text"
                    class="w-full h-10 pl-10 pr-4 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none bg-surface-container-lowest transition-colors"
                    id="searchPersonilInput"
                    placeholder="Cari nama personil..."
                    oninput="filterPersonil()"
                >
            </div>
        </div>
        <div class="p-2 overflow-y-auto flex-1">
            <div class="flex flex-col gap-1" id="personilSearchList">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
        <div class="p-4 border-t border-border-subtle bg-surface-container-lowest rounded-b-xl flex justify-end">
            <button type="button" class="px-5 py-2 bg-primary text-white text-[13px] font-bold rounded-lg hover:bg-primary/90 transition-colors" onclick="closePersonilModal()">
                Selesai
            </button>
        </div>
    </div>
</div>

<!-- Modal Pencarian Personil untuk Role (PJ, PT, KT) -->
<div class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" id="modalRole">
    <div class="bg-surface-card rounded-xl max-w-[600px] w-full max-h-[85vh] flex flex-col shadow-xl border border-border-subtle">
        <div class="p-5 border-b border-border-subtle flex justify-between items-center bg-surface-container-lowest rounded-t-xl">
            <h3 class="font-bold text-label-lg text-primary flex items-center gap-2" id="modalRoleTitle">
                <span class="material-symbols-outlined text-[20px]">person_search</span>
                <span id="modalRoleTitleText">Pilih Personil</span>
            </h3>
            <button type="button" class="text-on-surface-variant hover:text-primary transition-colors focus:outline-none" onclick="closeRoleModal()">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>
        <div class="p-4 border-b border-border-subtle">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
                <input
                    type="text"
                    class="w-full h-10 pl-10 pr-4 rounded-lg border border-outline-variant text-body-sm focus:ring-1 focus:ring-primary focus:border-primary focus:outline-none bg-surface-container-lowest transition-colors"
                    id="searchRoleInput"
                    placeholder="Cari nama personil..."
                    oninput="filterRolePersonil()"
                >
            </div>
        </div>
        <div class="p-2 overflow-y-auto flex-1">
            <div class="flex flex-col gap-1" id="rolePersonilSearchList">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* Maintain some custom classes for JS injection styling if necessary, 
   though JS should ideally inject Tailwind classes. We'll map them just in case. */
.personil-item-edit {
    display: flex; align-items: center; gap: 12px; padding: 12px;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
}
.personil-item-edit:hover { border-color: #8b5cf6; }
.personil-item-edit.empty { justify-content: center; border-style: dashed; cursor: pointer; color: #64748b; font-style: italic; }
.personil-item-edit.disabled-role { opacity: 0.6; cursor: not-allowed; background: #f1f5f9; }
.personil-role-edit { 
    background: #7c3aed; color: white; font-size: 10px; padding: 4px 8px; 
    border-radius: 6px; font-weight: bold; text-transform: uppercase; text-align: center; width: 100px; shrink: 0;
}
.personil-details-edit { flex: 1; min-width: 0; }
.personil-name-edit { font-weight: 600; font-size: 13px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.personil-info-edit { font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.btn-remove-personil-edit { 
    width: 28px; height: 28px; border-radius: 50%; background: #fee2e2; color: #ef4444; 
    display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s;
}
.btn-remove-personil-edit:hover { background: #ef4444; color: white; }

.personil-search-item {
    padding: 12px 16px; border-radius: 8px; border: 1px solid transparent; cursor: pointer;
    display: flex; align-items: center; justify-content: space-between; transition: all 0.2s;
}
.personil-search-item:hover { background: #f8fafc; border-color: #cbd5e1; }
.personil-search-item.selected { background: #f3f0ff; border-color: #8b5cf6; }
.personil-search-item.disabled { opacity: 0.5; cursor: not-allowed; background: #f1f5f9; }
.personil-search-info { display: flex; flex-direction: column; }
.personil-search-name { font-weight: 600; font-size: 13px; color: #0f172a; }
.personil-search-details { font-size: 11px; color: #64748b; }
.personil-checkbox-label { display: flex; align-items: center; gap: 12px; width: 100%; cursor: pointer; margin: 0; }
.personil-checkbox-label input { width: 18px; height: 18px; accent-color: #7c3aed; }
</style>

<script>
    // Inject pegawai data from server into the global variable FIRST
    var pegawaiData = @json($pegawai);
    console.log('Pegawai data injected:', pegawaiData.length, 'items');
</script>
<script src="{{ asset('js/pengawasan-personil.js') }}"></script>
<script>
// Toggle modal visibility overriding JS classes since we changed modal HTML structure
const originalCloseRole = window.closeRoleModal;
window.closeRoleModal = function() {
    if(originalCloseRole) originalCloseRole();
    document.getElementById('modalRole').classList.add('hidden');
    document.getElementById('modalRole').classList.remove('flex');
    document.body.style.overflow = '';
};
const originalOpenRole = window.openRoleModal;
window.openRoleModal = function(role) {
    if(originalOpenRole) originalOpenRole(role);
    document.getElementById('modalRole').classList.remove('hidden');
    document.getElementById('modalRole').classList.add('flex');
};

const originalClosePersonil = window.closePersonilModal;
window.closePersonilModal = function() {
    if(originalClosePersonil) originalClosePersonil();
    document.getElementById('modalPersonil').classList.add('hidden');
    document.getElementById('modalPersonil').classList.remove('flex');
    document.body.style.overflow = '';
};
const originalOpenPersonil = window.openPersonilModal;
window.openPersonilModal = function() {
    if(originalOpenPersonil) originalOpenPersonil();
    document.getElementById('modalPersonil').classList.remove('hidden');
    document.getElementById('modalPersonil').classList.add('flex');
};

// Dasar Hukum Functions
function addDasarHukum() {
    const container = document.getElementById('dasar-hukum-container');
    const newItem = document.createElement('div');
    newItem.className = 'dasar-hukum-item flex gap-2 items-start relative group mt-2';
    newItem.innerHTML = `
        <textarea
            name="dasar_hukum[]"
            class="flex-1 p-3 rounded-lg border border-outline-variant focus:ring-1 focus:ring-primary focus:border-primary text-body-sm bg-surface-container-lowest focus:outline-none transition-colors min-h-[60px]"
            placeholder="Masukkan dasar hukum"
            required
        ></textarea>
        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors shrink-0 mt-1" onclick="removeDasarHukum(this)" title="Hapus">
            <span class="material-symbols-outlined text-[18px]">delete</span>
        </button>
    `;
    container.appendChild(newItem);
}

function removeDasarHukum(button) {
    const container = document.getElementById('dasar-hukum-container');
    const items = container.querySelectorAll('.dasar-hukum-item');

    if (items.length > 1) {
        button.closest('.dasar-hukum-item').remove();
    } else {
        alert('Minimal harus ada 1 dasar hukum!');
    }
}

function togglePenandatangan() {
    const isPlh = document.getElementById('penandatangan_plh').checked;
    document.getElementById('penandatangan_plh_fields').style.display = isPlh ? '' : 'none';
    document.getElementById('penandatangan_definitif_info').style.display = isPlh ? 'none' : '';

    const namaHidden = document.getElementById('penandatangan_plh_nama');
    const namaSearch = document.getElementById('penandatangan_plh_search');
    const jabatanInput = document.getElementById('penandatangan_plh_jabatan');
    namaSearch.required = isPlh;
    jabatanInput.required = isPlh;
    if (!isPlh) {
        namaHidden.value = '';
        namaSearch.value = '';
        jabatanInput.value = '';
        document.getElementById('plh_dropdown').style.display = 'none';
    }
}

var plhSearchResults = [];

function filterPlhPegawai(query) {
    const dropdown = document.getElementById('plh_dropdown');
    if (!query) {
        dropdown.style.display = 'none';
        return;
    }
    const lower = query.toLowerCase();
    plhSearchResults = pegawaiData.filter(p => p.nama.toLowerCase().includes(lower)).slice(0, 10);
    if (plhSearchResults.length === 0) {
        dropdown.innerHTML = '<div class="px-4 py-3 text-on-surface-variant text-[13px]">Tidak ada hasil</div>';
    } else {
        dropdown.innerHTML = plhSearchResults.map((p, i) => `
            <div onclick="selectPlhPegawai(${i})" class="px-4 py-2 cursor-pointer hover:bg-primary/5 border-b border-border-subtle last:border-0 transition-colors">
                <div class="font-bold text-[13px] text-on-surface">${p.nama}</div>
                <div class="text-[11px] text-on-surface-variant">${p.jabatan || '-'}</div>
            </div>
        `).join('');
    }
    dropdown.style.display = 'block';
}

function showPlhDropdown() {
    const query = document.getElementById('penandatangan_plh_search').value;
    if (query) filterPlhPegawai(query);
}

function selectPlhPegawai(index) {
    const p = plhSearchResults[index];
    document.getElementById('penandatangan_plh_nama').value = p.nama;
    document.getElementById('penandatangan_plh_search').value = p.nama;
    document.getElementById('penandatangan_plh_jabatan').value = p.jabatan || '';
    document.getElementById('plh_dropdown').style.display = 'none';
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('penandatangan_plh_search');
    const dropdown = document.getElementById('plh_dropdown');
    if (wrapper && dropdown && !wrapper.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// Status change listener
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const statusHelpText = document.getElementById('statusHelpText');
    const fileInput = document.getElementById('file_laporan');
    const hasExistingFile = {{ $pengawasan->file_laporan ? 'true' : 'false' }};
    
    if(statusSelect && statusHelpText) {
        statusSelect.addEventListener('change', function() {
            if(this.value === 'selesai' && !hasExistingFile && !fileInput.files.length) {
                statusHelpText.style.display = 'flex';
                this.classList.add('border-amber-500', 'focus:ring-amber-500');
            } else {
                statusHelpText.style.display = 'none';
                this.classList.remove('border-amber-500', 'focus:ring-amber-500');
            }
        });
        
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                if(statusSelect.value === 'selesai' && this.files.length > 0) {
                    statusHelpText.style.display = 'none';
                    statusSelect.classList.remove('border-amber-500', 'focus:ring-amber-500');
                }
            });
        }
    }
});
</script>
@endpush