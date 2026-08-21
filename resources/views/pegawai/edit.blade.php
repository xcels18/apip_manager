@extends('layouts.app')

@section('title', 'Edit Data Pegawai')

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
        <span class="text-primary font-bold">Edit Pegawai</span>
    </nav>
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Ubah Data Pegawai</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Perbarui informasi detail pegawai pada sistem.</p>
        </div>
        <a href="{{ route('pegawai.index') }}" class="bg-surface-container-high text-primary h-10 px-4 rounded-lg flex items-center gap-2 hover:bg-surface-container-highest transition-colors focus:outline-none border border-outline-variant font-semibold text-[13px]">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop">
    <!-- Form Card -->
    <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden w-full max-w-4xl">
        <div class="px-6 py-4 border-b border-border-subtle flex items-center gap-2 bg-surface-container-lowest">
            <span class="material-symbols-outlined text-primary">edit_document</span>
            <h2 class="font-bold text-label-lg text-primary">Formulir Edit Data</h2>
        </div>

        <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-[13px] font-bold text-on-surface mb-2">NIP <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="nip" 
                        name="nip" 
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('nip') ? 'border-error focus:ring-error focus:border-error' : 'border-outline-variant focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors" 
                        value="{{ old('nip', $pegawai->nip) }}" 
                        placeholder="Contoh: 198001012010011001" 
                        maxlength="30" 
                        required
                    >
                    @error('nip')
                        <div class="text-error text-[11px] font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama" class="block text-[13px] font-bold text-on-surface mb-2">Nama Lengkap <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('nama') ? 'border-error focus:ring-error focus:border-error' : 'border-outline-variant focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors" 
                        value="{{ old('nama', $pegawai->nama) }}" 
                        placeholder="Nama Lengkap dengan Gelar" 
                        required
                    >
                    @error('nama')
                        <div class="text-error text-[11px] font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Pangkat/Golongan -->
                <div>
                    <label for="golongan" class="block text-[13px] font-bold text-on-surface mb-2">Pangkat / Golongan <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="golongan" 
                        name="golongan" 
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('golongan') ? 'border-error focus:ring-error focus:border-error' : 'border-outline-variant focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors" 
                        value="{{ old('golongan', $pegawai->golongan) }}" 
                        placeholder="Contoh: Penata Tingkat I / III/d" 
                        required
                    >
                    @error('golongan')
                        <div class="text-error text-[11px] font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="jabatan" class="block text-[13px] font-bold text-on-surface mb-2">Jabatan <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="jabatan" 
                        name="jabatan" 
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('jabatan') ? 'border-error focus:ring-error focus:border-error' : 'border-outline-variant focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors" 
                        value="{{ old('jabatan', $pegawai->jabatan) }}" 
                        placeholder="Contoh: Auditor Ahli Madya" 
                        required
                    >
                    @error('jabatan')
                        <div class="text-error text-[11px] font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-[13px] font-bold text-on-surface mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="w-full h-10 px-3 rounded-lg border {{ $errors->has('email') ? 'border-error focus:ring-error focus:border-error' : 'border-outline-variant focus:ring-primary focus:border-primary' }} text-body-sm bg-surface-container-lowest focus:outline-none focus:ring-1 transition-colors" 
                        value="{{ old('email', $pegawai->email) }}" 
                        placeholder="Contoh: pegawai@puncakjaya.go.id"
                    >
                    @error('email')
                        <div class="text-error text-[11px] font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border-subtle flex items-center justify-end gap-3">
                <a href="{{ route('pegawai.index') }}" class="px-5 py-2.5 rounded-lg font-bold text-[13px] text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg font-bold text-[13px] bg-primary text-on-primary hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
