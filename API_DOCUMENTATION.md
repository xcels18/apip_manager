> Pembaruan 9 September 2026: API pegawai lokal hanya-baca. API pengawasan memakai snapshot dari direktori eksternal dan transaksi atomik. Login dibatasi 5 percobaan/menit per email+IP serta 30/menit per IP (429 jika terlampaui). Pagination API pengawasan dibatasi 1–100 data/halaman.

# API Documentation - Sistem Pengawasan Inspektorat

## Base URL
```
http://127.0.0.1:8001/api
```

## ⚠️ Penting: Perbedaan Web Routes dan API Routes

Aplikasi ini memiliki 2 jenis routes yang berbeda:

1. **Web Routes** (untuk aplikasi web browser):
   - `/pegawai` → Halaman web pegawai
   - `/pengawasan` → Halaman web pengawasan
   - Menggunakan session authentication
   - Tidak perlu token

2. **API Routes** (untuk integrasi dengan aplikasi lain):
   - `/api/pegawai` → API endpoint pegawai
   - `/api/pengawasan` → API endpoint pengawasan
   - Menggunakan token authentication (Sanctum)
   - Perlu token di header `Authorization: Bearer {token}`

**Jangan akses `/api/pegawai` dari browser!** Gunakan `/pegawai` untuk halaman web.

## Authentication
API ini menggunakan **Laravel Sanctum** untuk autentikasi berbasis token.

### Headers yang Diperlukan
Untuk endpoint yang memerlukan autentikasi, sertakan header berikut:
```
Authorization: Bearer {your-token}
Accept: application/json
Content-Type: application/json
```

---

## 1. Authentication Endpoints

### 1.1 Login
Mendapatkan token autentikasi.

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "nip": "199001012020121001",
      "jabatan": "Inspektur"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

**Response Error (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 1.2 Logout
Menghapus token autentikasi.

**Endpoint:** `POST /api/logout`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

### 1.3 Get User Info
Mendapatkan informasi user yang sedang login.

**Endpoint:** `GET /api/me`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "nip": "199001012020121001",
    "jabatan": "Inspektur"
  }
}
```

---

## 2. Pengawasan Endpoints

### 2.1 Get All Pengawasan
Mendapatkan daftar semua pengawasan dengan pagination.

**Endpoint:** `GET /api/pengawasan`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional): `belum_selesai` atau `selesai`
- `jenis_penugasan` (optional): `Audit`, `Reviu`, `Monitoring`, `Evaluasi`, `Perjalanan Dinas Luar Daerah`
- `tahun` (optional): Tahun (contoh: `2025`)
- `bulan` (optional): Bulan (1-12)
- `per_page` (optional): Jumlah data per halaman (default: 15)
- `page` (optional): Nomor halaman (default: 1)

**Example Request:**
```
GET /api/pengawasan?status=belum_selesai&tahun=2025&per_page=10&page=1
```

**Response Success (200):**
```json
{
  "data": [
    {
      "id": 1,
      "nomor_st": "100.3.5.4/001/ST/ITKAB/2025",
      "tanggal_st": "2025-01-15",
      "lama_penugasan": 7,
      "jenis_penugasan": "Audit",
      "uraian_penugasan": "Audit Keuangan Daerah",
      "lokasi_penugasan": "Kantor Dinas Pendidikan",
      "alat_angkut": "darat",
      "status": "belum_selesai",
      "status_label": "Belum Selesai",
      "file_laporan": null,
      "penanggung_jawab": {
        "id": 1,
        "nip": "199001012020121001",
        "nama": "John Doe",
        "jabatan": "Inspektur",
        "golongan": "IV/a"
      },
      "pengendali_teknis": {
        "id": 2,
        "nip": "199002022020122002",
        "nama": "Jane Smith",
        "jabatan": "Pengendali Teknis",
        "golongan": "III/d"
      },
      "ketua_tim": {
        "id": 3,
        "nip": "199003032020123003",
        "nama": "Bob Johnson",
        "jabatan": "Auditor",
        "golongan": "III/c"
      },
      "anggota": [
        {
          "id": 4,
          "nip": "199004042020124004",
          "nama": "Alice Brown",
          "jabatan": "Auditor",
          "golongan": "III/b"
        }
      ],
      "dasar_hukum": [
        {
          "id": 1,
          "isi_dasar": "Surat Perintah Tugas No. 001/2025"
        }
      ],
      "created_at": "2025-01-10 10:00:00",
      "updated_at": "2025-01-10 10:00:00"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8001/api/pengawasan?page=1",
    "last": "http://127.0.0.1:8001/api/pengawasan?page=5",
    "prev": null,
    "next": "http://127.0.0.1:8001/api/pengawasan?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

---

### 2.2 Get Single Pengawasan
Mendapatkan detail pengawasan berdasarkan ID.

**Endpoint:** `GET /api/pengawasan/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nomor_st": "100.3.5.4/001/ST/ITKAB/2025",
    "tanggal_st": "2025-01-15",
    "lama_penugasan": 7,
    "jenis_penugasan": "Audit",
    "uraian_penugasan": "Audit Keuangan Daerah",
    "lokasi_penugasan": "Kantor Dinas Pendidikan",
    "alat_angkut": "darat",
    "status": "belum_selesai",
    "status_label": "Belum Selesai",
    "file_laporan": null,
    "penanggung_jawab": { ... },
    "pengendali_teknis": { ... },
    "ketua_tim": { ... },
    "anggota": [ ... ],
    "dasar_hukum": [ ... ],
    "created_at": "2025-01-10 10:00:00",
    "updated_at": "2025-01-10 10:00:00"
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Pengawasan not found"
}
```

---

### 2.3 Create Pengawasan
Membuat data pengawasan baru.

**Endpoint:** `POST /api/pengawasan`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "nomor_st": "100.3.5.4/002/ST/ITKAB/2025",
  "tanggal_st": "2025-01-20",
  "lama_penugasan": 5,
  "jenis_penugasan": "Audit",
  "uraian_penugasan": "Audit Kinerja",
  "lokasi_penugasan": "Kantor Dinas Kesehatan",
  "alat_angkut": "darat",
  "status": "belum_selesai",
  "penanggung_jawab_id": 1,
  "pengendali_teknis_id": 2,
  "ketua_tim_id": 3,
  "anggota": [4, 5],
  "dasar_hukum": [
    "Surat Perintah Tugas No. 002/2025",
    "Peraturan Bupati No. 10 Tahun 2024"
  ]
}
```

**Field Descriptions:**
- `nomor_st` (required): Nomor Surat Tugas unik, maksimal 100 karakter
- `tanggal_st` (required): Tanggal Surat Tugas (format: YYYY-MM-DD)
- `lama_penugasan` (required): Lama penugasan dalam hari (integer, 1–366)
- `jenis_penugasan` (required): Jenis penugasan (`Audit`, `Reviu`, `Monitoring`, `Evaluasi`, `Perjalanan Dinas Luar Daerah`)
- `uraian_penugasan` (required): Uraian/deskripsi penugasan
- `lokasi_penugasan` (required): Lokasi penugasan
- `alat_angkut` (required): Alat angkut (`darat`, `udara`)
- `status` (required): Status (`belum_selesai`, `selesai`)
- `penanggung_jawab_id`: ID pegawai eksternal; wajib kecuali Perjalanan Dinas Luar Daerah
- `pengendali_teknis_id` (nullable): ID Pegawai sebagai Pengendali Teknis
- `ketua_tim_id`: ID pegawai eksternal; wajib kecuali Perjalanan Dinas Luar Daerah
- `anggota` (nullable): Array ID Pegawai sebagai Anggota Tim
- `dasar_hukum` (required): Array string berisi dasar hukum (minimal 1)

**Response Success (201):**
```json
{
  "success": true,
  "message": "Pengawasan created successfully",
  "data": { ... }
}
```

**Response Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nomor_st": ["The nomor st field is required."],
    "tanggal_st": ["The tanggal st field is required."]
  }
}
```


---

### 2.4 Update Pengawasan
Mengupdate data pengawasan yang sudah ada.

**Endpoint:** `PUT /api/pengawasan/{id}` atau `PATCH /api/pengawasan/{id}`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "status": "selesai",
  "uraian_penugasan": "Audit Kinerja - Updated"
}
```

**Note:** Semua field bersifat optional. Hanya field yang dikirim yang akan diupdate. Status `selesai` memerlukan laporan yang sudah diunggah melalui aplikasi. ID personil harus berasal dari direktori eksternal; duplikasi personil dan bentrok tanggal ketua/anggota ditolak dengan 422. Snapshot personil yang tidak berubah dipertahankan walaupun layanan pegawai offline.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Pengawasan updated successfully",
  "data": { ... }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Pengawasan not found"
}
```

---

### 2.5 Delete Pengawasan
Menghapus data pengawasan.

**Endpoint:** `DELETE /api/pengawasan/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "message": "Pengawasan deleted successfully"
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Pengawasan not found"
}
```

---

## 3. Pegawai Endpoints

### 3.1 Get All Pegawai
Mendapatkan daftar semua pegawai dengan pagination.

**Endpoint:** `GET /api/pegawai`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `search` (optional): Cari berdasarkan nama atau NIP
- `per_page` (optional): Jumlah data per halaman (default: 15)
- `page` (optional): Nomor halaman (default: 1)

**Example Request:**
```
GET /api/pegawai?search=john&per_page=10
```

**Response Success (200):**
```json
{
  "data": [
    {
      "id": 1,
      "nip": "199001012020121001",
      "nama": "John Doe",
      "jabatan": "Inspektur",
      "golongan": "IV/a",
      "created_at": "2025-01-01 10:00:00",
      "updated_at": "2025-01-01 10:00:00"
    }
  ],
  "meta": { ... }
}
```

---

### 3.2 Get Single Pegawai
Mendapatkan detail pegawai berdasarkan ID.

**Endpoint:** `GET /api/pegawai/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nip": "199001012020121001",
    "nama": "John Doe",
    "jabatan": "Inspektur",
    "golongan": "IV/a",
    "created_at": "2025-01-01 10:00:00",
    "updated_at": "2025-01-01 10:00:00"
  }
}
```

**Response Error (404):**
```json
{
  "message": "Not Found"
}
```

---

### 3.3 Pengelolaan pegawai

APIP Manager menggunakan direktori pegawai eksternal sebagai sumber data. Endpoint lokal hanya menyediakan `GET /api/pegawai` dan `GET /api/pegawai/{id}`. Tambah, ubah, dan hapus pegawai dilakukan di aplikasi sumber; metode POST/PUT/PATCH/DELETE lokal mengembalikan 405 dan tidak diteruskan ke layanan eksternal.

Set `PEGAWAI_API_URL` dan token layanan, atau gunakan Pengaturan Sistem. URL harus menunjuk ke direktori eksternal, bukan `/api/pegawai` milik APIP Manager sendiri. Gangguan koneksi atau respons upstream tidak valid menghasilkan 503. Pagination list menerima `page` positif dan `per_page` 1–100. Metadata minimal: `total`, `per_page`, `current_page`; `last_page` tersedia jika dikirim upstream. Field tambahan pegawai mengikuti layanan sumber.

---

## Error Responses

### 401 Unauthorized
Token tidak valid atau tidak ada.
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
Data yang dikirim tidak valid.
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Internal Server Error
Terjadi kesalahan di server.
```json
{
  "success": false,
  "message": "Failed to process request",
  "error": "Error details"
}
```

---

## Testing dengan Postman/cURL

### Example: Login
```bash
curl -X POST http://127.0.0.1:8001/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

### Example: Get Pengawasan (dengan token)
```bash
curl -X GET "http://127.0.0.1:8001/api/pengawasan?status=belum_selesai" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Example: Create Pengawasan
```bash
curl -X POST http://127.0.0.1:8001/api/pengawasan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nomor_st": "100.3.5.4/003/ST/ITKAB/2025",
    "tanggal_st": "2025-01-25",
    "lama_penugasan": 3,
    "jenis_penugasan": "Monitoring",
    "uraian_penugasan": "Monitoring Program",
    "lokasi_penugasan": "Kantor Dinas",
    "alat_angkut": "darat",
    "status": "belum_selesai",
    "penanggung_jawab_id": 1,
    "pengendali_teknis_id": 2,
    "ketua_tim_id": 3,
    "anggota": [4],
    "dasar_hukum": ["Surat Perintah No. 003/2025"]
  }'
```

---

## Notes

1. **Pagination**: Semua endpoint list menggunakan pagination. Gunakan parameter `per_page` dan `page` untuk navigasi.

2. **Filtering**: Endpoint pengawasan mendukung filtering berdasarkan status, jenis, tahun, dan bulan.

3. **Relationships**: Data pengawasan otomatis include relasi (penanggung_jawab, pengendali_teknis, ketua_tim, anggota, dasar_hukum).

4. **Nullable Fields**: Untuk jenis penugasan "Perjalanan Dinas Luar Daerah", field `penanggung_jawab_id`, `pengendali_teknis_id`, dan `ketua_tim_id` bisa null.

5. **Token Expiration**: Token tidak memiliki expiration time secara default. Untuk keamanan, implementasikan token rotation atau expiration sesuai kebutuhan.

6. **Rate Limiting**: Pertimbangkan untuk menambahkan rate limiting pada production environment.


