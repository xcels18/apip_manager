<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function index()
    {
        $apiUrl = \App\Models\SystemSetting::valueFor('pegawai_api_url') ?? config('services.pegawai.url');
        $apiToken = \App\Models\SystemSetting::valueFor('pegawai_api_token') ?? config('services.pegawai.token');

        $definitifNama = \App\Models\SystemSetting::valueFor('definitif_nama') ?? 'BOTTEN TANDIPADA';
        $definitifNip = \App\Models\SystemSetting::valueFor('definitif_nip') ?? '196612141995031001';
        $definitifJabatan = \App\Models\SystemSetting::valueFor('definitif_jabatan') ?? 'Plt. Inspektur';
        $definitifPangkat = \App\Models\SystemSetting::valueFor('definitif_pangkat') ?? 'Pembina Utama Muda (IV/c)';

        $kopPemerintah = \App\Models\SystemSetting::valueFor('kop_pemerintah') ?? 'PEMERINTAH KABUPATEN PUNCAK JAYA';
        $kopInstansi = \App\Models\SystemSetting::valueFor('kop_instansi') ?? 'INSPEKTORAT';
        $kopJalan = \App\Models\SystemSetting::valueFor('kop_jalan') ?? 'Jalan Yos Sudarso Kotaraja Telp. (0969) 31014 Fax. (0969) 31015';
        $kopEmail = \App\Models\SystemSetting::valueFor('kop_email') ?? 'Email: inspektorat@puncakjayakab.go.id';
        $formatNomorSt = \App\Models\SystemSetting::valueFor('format_nomor_st') ?? '100.3.5.4/[NOMOR]/ST/ITKAB/[TAHUN]';

        return view('setting.index', compact('apiUrl', 'apiToken', 'definitifNama', 'definitifNip', 'definitifJabatan', 'definitifPangkat', 'kopPemerintah', 'kopInstansi', 'kopJalan', 'kopEmail', 'formatNomorSt'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'email' => $request->email,
        ]);

        return redirect()->route('setting.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            'new_password.min' => 'Password minimal 8 karakter',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->route('setting.index')
                ->withErrors(['current_password' => 'Password lama tidak sesuai'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('setting.index')
            ->with('success', 'Password berhasil diubah!');
    }

    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'pegawai_api_url' => 'required|url',
            'pegawai_api_token' => 'required|string',
            'definitif_nama' => 'required|string',
            'definitif_nip' => 'nullable|string',
            'definitif_jabatan' => 'required|string',
            'definitif_pangkat' => 'nullable|string',
            'kop_pemerintah' => 'required|string',
            'kop_instansi' => 'required|string',
            'kop_jalan' => 'required|string',
            'kop_email' => 'required|string',
            'format_nomor_st' => 'required|string',
        ], [
            'pegawai_api_url.required' => 'URL API Pegawai harus diisi',
            'pegawai_api_url.url' => 'Format URL tidak valid',
            'pegawai_api_token.required' => 'Token API Pegawai harus diisi',
            'definitif_nama.required' => 'Nama Penandatangan Definitif harus diisi',
            'definitif_jabatan.required' => 'Jabatan Penandatangan Definitif harus diisi',
            'kop_pemerintah.required' => 'Header Pemerintah harus diisi',
            'kop_instansi.required' => 'Subheader Instansi harus diisi',
            'kop_jalan.required' => 'Alamat harus diisi',
            'kop_email.required' => 'Email harus diisi',
            'format_nomor_st.required' => 'Format Nomor Surat Tugas harus diisi',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            foreach ($validated as $key => $value) {
                \App\Models\SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });
        app(\App\Services\SettingsService::class)->forget();

        return redirect()->route('setting.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
