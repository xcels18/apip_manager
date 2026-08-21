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
        $apiUrl = \App\Models\SystemSetting::where('key', 'pegawai_api_url')->first()->value ?? config('services.pegawai.url');
        $apiToken = \App\Models\SystemSetting::where('key', 'pegawai_api_token')->first()->value ?? config('services.pegawai.token');

        return view('setting.index', compact('apiUrl', 'apiToken'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
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
        if (!Hash::check($request->current_password, $user->password)) {
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
        $request->validate([
            'pegawai_api_url' => 'required|url',
            'pegawai_api_token' => 'required|string',
        ], [
            'pegawai_api_url.required' => 'URL API Pegawai harus diisi',
            'pegawai_api_url.url' => 'Format URL tidak valid',
            'pegawai_api_token.required' => 'Token API Pegawai harus diisi',
        ]);

        \App\Models\SystemSetting::updateOrCreate(
            ['key' => 'pegawai_api_url'],
            ['value' => $request->pegawai_api_url]
        );

        \App\Models\SystemSetting::updateOrCreate(
            ['key' => 'pegawai_api_token'],
            ['value' => $request->pegawai_api_token]
        );

        return redirect()->route('setting.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
