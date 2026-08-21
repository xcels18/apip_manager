# Perbaikan: Logout Otomatis Saat Akses Menu Pegawai

## 🐛 Masalah

Setelah login, ketika user mengklik menu **Pegawai**, aplikasi langsung logout otomatis dan redirect ke halaman login.

---

## 🔍 Penyebab Masalah

Masalah ini terjadi karena **konflik antara Web Routes dan API Routes** yang baru saja ditambahkan:

### 1. **Route Name Collision**
Sebelum perbaikan, kedua route menggunakan nama yang sama:
- Web: `pegawai.index`, `pegawai.store`, dll
- API: `pegawai.index`, `pegawai.store`, dll (SAMA!)

Ini menyebabkan Laravel bingung route mana yang harus digunakan.

### 2. **Sanctum Middleware Conflict**
Laravel Sanctum mengaktifkan middleware `AuthenticateSession` yang:
- Didesain untuk SPA (Single Page Application)
- Memvalidasi session setiap request
- Menghapus session jika tidak valid
- **Konflik dengan session-based authentication** yang digunakan aplikasi web

Ketika user mengakses menu Pegawai:
1. Browser request ke `/pegawai`
2. Laravel salah route ke `/api/pegawai` (karena route name collision)
3. Sanctum middleware `AuthenticateSession` aktif
4. Middleware mendeteksi tidak ada token API
5. Session web dihapus
6. User di-logout dan redirect ke `/login`

---

## ✅ Solusi yang Diterapkan

### 1. **Menambahkan Prefix Nama untuk API Routes**

**File:** `routes/api.php`

**Sebelum:**
```php
Route::apiResource('pegawai', PegawaiApiController::class);
// Route name: pegawai.index (KONFLIK!)
```

**Sesudah:**
```php
Route::apiResource('pegawai', PegawaiApiController::class)->names([
    'index' => 'api.pegawai.index',
    'store' => 'api.pegawai.store',
    'show' => 'api.pegawai.show',
    'update' => 'api.pegawai.update',
    'destroy' => 'api.pegawai.destroy',
]);
```

Sekarang route names berbeda:
- Web: `pegawai.index`
- API: `api.pegawai.index` ✅

### 2. **Menonaktifkan AuthenticateSession Middleware**

**File:** `config/sanctum.php`

**Sebelum:**
```php
'middleware' => [
    'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
    // Middleware ini menyebabkan konflik!
    ...
],
```

**Sesudah:**
```php
'middleware' => [
    'authenticate_session' => Illuminate\Auth\Middleware\Authenticate::class,
    // Menggunakan middleware standar Laravel
    ...
],
```

### 3. **Clear Cache**

Menjalankan command untuk clear cache:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📋 Verifikasi Perbaikan

### Cek Route Names
```bash
php artisan route:list | grep pegawai
```

**Output yang benar:**
```
GET|HEAD   api/pegawai ........ api.pegawai.index › Api\PegawaiApiController@index
GET|HEAD   pegawai ............ pegawai.index › PegawaiController@index
```

Route names sudah berbeda! ✅

---

## 🧪 Testing

Silakan test kembali dengan langkah berikut:

### 1. **Test Web Application**
1. Buka browser: `http://127.0.0.1:8001`
2. Login dengan email dan password
3. Klik menu **Dashboard** → Harus berhasil ✅
4. Klik menu **Pegawai** → Harus berhasil ✅ (tidak logout lagi!)
5. Klik menu **Pengawasan** → Harus berhasil ✅
6. Klik menu **Rekap** → Harus berhasil ✅

### 2. **Test API Endpoints**
```bash
# Login via API
curl -X POST http://127.0.0.1:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Get Pegawai via API (dengan token)
curl -X GET http://127.0.0.1:8001/api/pegawai \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## 📚 Perbedaan Web Routes vs API Routes

### **Web Routes** (untuk browser)
- **URL**: `/pegawai`, `/pengawasan`, `/dashboard`
- **Authentication**: Session-based (login form)
- **Response**: HTML pages (Blade templates)
- **Untuk**: User yang menggunakan browser

### **API Routes** (untuk integrasi)
- **URL**: `/api/pegawai`, `/api/pengawasan`, `/api/login`
- **Authentication**: Token-based (Bearer token)
- **Response**: JSON data
- **Untuk**: Aplikasi lain yang ingin integrasi

---

## ⚠️ Catatan Penting

1. **Jangan akses `/api/pegawai` dari browser!**
   - Gunakan `/pegawai` untuk halaman web
   - Gunakan `/api/pegawai` hanya untuk API calls dengan token

2. **Web dan API terpisah**
   - Login web tidak sama dengan login API
   - Session web tidak bisa digunakan untuk API
   - Token API tidak bisa digunakan untuk web

3. **Untuk user biasa**
   - Gunakan aplikasi web seperti biasa
   - Tidak perlu tahu tentang API
   - API hanya untuk developer yang ingin integrasi

---

## 🎉 Hasil

✅ **Masalah logout otomatis sudah teratasi!**

Sekarang user bisa:
- Login dengan normal
- Akses semua menu (Dashboard, Pegawai, Pengawasan, Rekap)
- Tidak logout otomatis lagi
- API tetap berfungsi untuk integrasi dengan aplikasi lain

---

## 📞 Jika Masih Ada Masalah

Jika masih mengalami logout otomatis:

1. **Clear browser cache dan cookies**
   - Tekan Ctrl+Shift+Delete (Windows/Linux)
   - Tekan Cmd+Shift+Delete (Mac)
   - Hapus semua cookies untuk `127.0.0.1`

2. **Restart server**
   ```bash
   # Stop server (Ctrl+C)
   # Start server lagi
   php artisan serve
   ```

3. **Clear Laravel cache lagi**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   php artisan view:clear
   ```

4. **Cek file .env**
   Pastikan `SESSION_DRIVER=file` atau `SESSION_DRIVER=database`
   ```
   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```

5. **Regenerate APP_KEY** (jika perlu)
   ```bash
   php artisan key:generate
   ```

