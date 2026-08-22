@extends('layouts.app')

@section('title', 'Setting Akun')

@section('content')
<!-- Breadcrumbs & Header -->
<div class="px-margin-desktop pt-8 pb-6 flex flex-col gap-4">
    <nav class="flex items-center text-on-surface-variant text-label-sm font-label-sm gap-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-primary font-bold">Setting Akun</span>
    </nav>
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Setting Akun</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
        </div>
    </div>
</div>

<div class="px-margin-desktop pb-margin-desktop">

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        <span class="text-[13px] font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Profile Settings -->
        <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">manage_accounts</span>
                <h2 class="font-bold text-label-lg text-primary">Informasi Profil</h2>
            </div>
            <div class="p-6 bg-white flex-1">
                <form action="{{ route('setting.update-profile') }}" method="POST" class="flex flex-col h-full gap-5">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('name') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                               value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">NIP <span class="text-on-surface-variant font-normal">(Opsional)</span></label>
                        <input type="text" name="nip" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('nip') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                               value="{{ old('nip', Auth::user()->nip) }}" placeholder="Contoh: 198001012005011001">
                        @error('nip')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Jabatan <span class="text-on-surface-variant font-normal">(Opsional)</span></label>
                        <input type="text" name="jabatan" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('jabatan') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                               value="{{ old('jabatan', Auth::user()->jabatan) }}" placeholder="Contoh: Auditor Muda">
                        @error('jabatan')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Email</label>
                        <input type="email" name="email" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('email') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                               value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-auto pt-4">
                        <button type="submit" class="w-full h-10 rounded-lg font-bold text-[13px] bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Settings -->
        <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">lock</span>
                <h2 class="font-bold text-label-lg text-primary">Ubah Password</h2>
            </div>
            <div class="p-6 bg-white flex-1">
                <form action="{{ route('setting.update-password') }}" method="POST" class="flex flex-col h-full gap-5">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Password Lama</label>
                        <input type="password" name="current_password" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('current_password') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors" required>
                        @error('current_password')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Password Baru</label>
                        <input type="password" name="new_password" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('new_password') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors" required>
                        @error('new_password')
                            <span class="text-error text-[11px]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[13px] font-bold text-on-surface">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors" required>
                    </div>

                    <div class="mt-auto pt-4">
                        <button type="submit" class="w-full h-10 rounded-lg font-bold text-[13px] bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- System API Settings -->
    <div class="mt-8 mb-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary text-[24px]">admin_panel_settings</span>
            <h2 class="font-bold text-headline-sm text-primary">Pengaturan Sistem</h2>
            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider ml-2">Khusus Admin</span>
        </div>

        <form action="{{ route('setting.update-system') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Konfigurasi KOP Surat -->
                <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">description</span>
                        <h3 class="font-bold text-label-lg text-primary">KOP Surat</h3>
                    </div>
                    <div class="p-6 bg-white flex flex-col gap-4 flex-1">
                        <div class="flex flex-col gap-2">
                            <label class="text-[13px] font-bold text-on-surface">Pemerintah Daerah (Baris 1) <span class="text-error">*</span></label>
                            <input type="text" name="kop_pemerintah" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('kop_pemerintah') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                    value="{{ old('kop_pemerintah', $kopPemerintah ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA') }}" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[13px] font-bold text-on-surface">Nama Instansi (Baris 2) <span class="text-error">*</span></label>
                            <input type="text" name="kop_instansi" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('kop_instansi') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                    value="{{ old('kop_instansi', $kopInstansi ?? 'INSPEKTORAT') }}" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[13px] font-bold text-on-surface">Alamat / Jalan (Baris 3) <span class="text-error">*</span></label>
                            <input type="text" name="kop_jalan" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('kop_jalan') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                    value="{{ old('kop_jalan', $kopJalan ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015') }}" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[13px] font-bold text-on-surface">Email / Website (Baris 4) <span class="text-error">*</span></label>
                            <input type="text" name="kop_email" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('kop_email') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                    value="{{ old('kop_email', $kopEmail ?? 'Email: inspektorat@puncakjayakab.go.id') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Penandatangan Definitif & API Integrasi -->
                <div class="flex flex-col gap-6">
                    <!-- Penandatangan Definitif -->
                    <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">signature</span>
                            <h3 class="font-bold text-label-lg text-primary">Penandatangan Definitif</h3>
                        </div>
                        <div class="p-6 bg-white flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-[13px] font-bold text-on-surface">Nama Lengkap <span class="text-error">*</span></label>
                                <input type="text" name="definitif_nama" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('definitif_nama') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                        value="{{ old('definitif_nama', $definitifNama) }}" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label class="text-[13px] font-bold text-on-surface">NIP</label>
                                    <input type="text" name="definitif_nip" class="w-full h-10 px-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary text-body-sm bg-white outline-none transition-colors"
                                            value="{{ old('definitif_nip', $definitifNip) }}">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[13px] font-bold text-on-surface">Jabatan <span class="text-error">*</span></label>
                                    <input type="text" name="definitif_jabatan" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('definitif_jabatan') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                            value="{{ old('definitif_jabatan', $definitifJabatan) }}" placeholder="Contoh: Plt. Inspektur" required>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[13px] font-bold text-on-surface">Pangkat / Golongan <span class="text-error">*</span></label>
                                <input type="text" name="definitif_pangkat" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('definitif_pangkat') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                        value="{{ old('definitif_pangkat', $definitifPangkat ?? 'Pembina Utama Muda (IV/c)') }}" placeholder="Contoh: Pembina Utama Muda (IV/c)" required>
                            </div>
                        </div>
                    </div>

                    <!-- Konfigurasi API -->
                    <div class="bg-surface-card border border-border-subtle rounded-xl shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-border-subtle bg-surface-container-lowest flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">api</span>
                            <h3 class="font-bold text-label-lg text-primary">Integrasi API Pegawai</h3>
                        </div>
                        <div class="p-6 bg-white flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-[13px] font-bold text-on-surface">URL Endpoint API <span class="text-error">*</span></label>
                                <input type="url" name="pegawai_api_url" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('pegawai_api_url') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors"
                                        value="{{ old('pegawai_api_url', $apiUrl) }}" placeholder="Contoh: http://localhost:8000/api/pegawai" required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[13px] font-bold text-on-surface">Bearer Token <span class="text-error">*</span></label>
                                <input type="text" name="pegawai_api_token" class="w-full h-10 px-3 rounded-lg border {{ $errors->has('pegawai_api_token') ? 'border-error focus:border-error focus:ring-error' : 'border-outline-variant focus:border-primary focus:ring-primary' }} focus:ring-1 text-body-sm bg-white outline-none transition-colors font-mono"
                                        value="{{ old('pegawai_api_token', $apiToken) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="h-12 px-8 rounded-lg font-bold text-[14px] bg-primary text-white hover:bg-primary/90 transition-colors shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Simpan Seluruh Pengaturan Sistem
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
