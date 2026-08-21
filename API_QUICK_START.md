# API Quick Start Guide

## 🚀 Cara Menggunakan API

### 1. Pastikan Server Berjalan
```bash
php artisan serve
```
Server akan berjalan di: `http://127.0.0.1:8000` atau `http://127.0.0.1:8001`

---

### 2. Login untuk Mendapatkan Token

**Endpoint:** `POST /api/login`

**Request:**
```bash
curl -X POST http://127.0.0.1:8001/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

**⚠️ PENTING:** Simpan token yang didapat untuk digunakan di request selanjutnya!

---

### 3. Gunakan Token untuk Request Lainnya

Setelah mendapat token, gunakan di header `Authorization`:

```bash
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 📋 Contoh Penggunaan

### A. Get List Pengawasan

```bash
curl -X GET "http://127.0.0.1:8001/api/pengawasan" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Dengan Filter:**
```bash
curl -X GET "http://127.0.0.1:8001/api/pengawasan?status=belum_selesai&tahun=2025&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

### B. Get Detail Pengawasan

```bash
curl -X GET "http://127.0.0.1:8001/api/pengawasan/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

### C. Create Pengawasan Baru

```bash
curl -X POST http://127.0.0.1:8001/api/pengawasan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nomor_st": "100.3.5.4/999/ST/ITKAB/2025",
    "tanggal_st": "2025-12-15",
    "lama_penugasan": 5,
    "jenis_penugasan": "Audit",
    "uraian_penugasan": "Audit Test via API",
    "lokasi_penugasan": "Kantor Test",
    "alat_angkut": "darat",
    "status": "belum_selesai",
    "penanggung_jawab_id": 1,
    "pengendali_teknis_id": 2,
    "ketua_tim_id": 3,
    "anggota": [4, 5],
    "dasar_hukum": [
      "Surat Perintah Tugas No. 999/2025",
      "Peraturan Test"
    ]
  }'
```

---

### D. Update Pengawasan

```bash
curl -X PUT http://127.0.0.1:8001/api/pengawasan/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "selesai",
    "uraian_penugasan": "Audit Test - Updated"
  }'
```

---

### E. Delete Pengawasan

```bash
curl -X DELETE http://127.0.0.1:8001/api/pengawasan/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

### F. Get List Pegawai

```bash
curl -X GET "http://127.0.0.1:8001/api/pegawai" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Dengan Search:**
```bash
curl -X GET "http://127.0.0.1:8001/api/pegawai?search=john&per_page=5" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

### G. Logout

```bash
curl -X POST http://127.0.0.1:8001/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 🔧 Testing dengan Postman

### 1. Import Collection
Buat collection baru di Postman dengan struktur:

```
Pengawasan API
├── Auth
│   ├── Login
│   ├── Logout
│   └── Get User Info
├── Pengawasan
│   ├── Get All
│   ├── Get Single
│   ├── Create
│   ├── Update
│   └── Delete
└── Pegawai
    ├── Get All
    ├── Get Single
    ├── Create
    ├── Update
    └── Delete
```

### 2. Setup Environment Variables
Buat environment dengan variables:
- `base_url`: `http://127.0.0.1:8001/api`
- `token`: (akan diisi setelah login)

### 3. Setup Authorization
Di Collection settings:
- Type: Bearer Token
- Token: `{{token}}`

---

## 📱 Integrasi dengan Aplikasi Lain

### JavaScript/Node.js Example

```javascript
const axios = require('axios');

const API_URL = 'http://127.0.0.1:8001/api';
let token = '';

// Login
async function login() {
  const response = await axios.post(`${API_URL}/login`, {
    email: 'admin@example.com',
    password: 'password'
  });
  token = response.data.data.token;
  return token;
}

// Get Pengawasan
async function getPengawasan() {
  const response = await axios.get(`${API_URL}/pengawasan`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return response.data;
}

// Usage
(async () => {
  await login();
  const data = await getPengawasan();
  console.log(data);
})();
```

### Python Example

```python
import requests

API_URL = 'http://127.0.0.1:8001/api'
token = ''

# Login
def login():
    global token
    response = requests.post(f'{API_URL}/login', json={
        'email': 'admin@example.com',
        'password': 'password'
    })
    token = response.json()['data']['token']
    return token

# Get Pengawasan
def get_pengawasan():
    headers = {
        'Authorization': f'Bearer {token}',
        'Accept': 'application/json'
    }
    response = requests.get(f'{API_URL}/pengawasan', headers=headers)
    return response.json()

# Usage
login()
data = get_pengawasan()
print(data)
```

---

## 📚 Dokumentasi Lengkap

Lihat file `API_DOCUMENTATION.md` untuk dokumentasi lengkap semua endpoint.

---

## ⚠️ Troubleshooting

### Error: "Unauthenticated"
- Pastikan token sudah disertakan di header `Authorization`
- Pastikan format: `Bearer YOUR_TOKEN_HERE`
- Token mungkin sudah expired atau dihapus

### Error: "Validation error"
- Periksa format data yang dikirim
- Pastikan semua field required sudah diisi
- Periksa tipe data (string, integer, array, dll)

### Error: "CORS"
- Jika mengakses dari domain berbeda, tambahkan CORS configuration
- Install: `composer require fruitcake/laravel-cors`
- Konfigurasi di `config/cors.php`

