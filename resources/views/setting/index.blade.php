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
</div>
@endsection
