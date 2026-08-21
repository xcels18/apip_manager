# Logo Kabupaten Puncak Jaya

## Logo Saat Ini

Saat ini menggunakan logo placeholder SVG yang embedded langsung di template PDF.

Logo berbentuk:
- Lingkaran biru (#1e40af)
- Gunung emas di tengah
- Teks "PUNCAK JAYA"
- Ukuran: 60x60 pixel

## Cara Mengganti dengan Logo Asli

Jika Anda memiliki logo asli Kabupaten Puncak Jaya, ikuti langkah berikut:

### Opsi 1: Menggunakan File PNG (Recommended)

1. **Siapkan file logo:**
   - Format: PNG dengan background transparan
   - Ukuran: 200x200 pixel atau lebih (akan di-resize otomatis)
   - Nama file: `logo-puncak-jaya.png`

2. **Upload file:**
   - Letakkan file di folder: `public/images/logo-puncak-jaya.png`

3. **Edit template PDF:**
   - Buka file: `resources/views/pengawasan/surat-tugas-pdf.blade.php`
   - Cari bagian logo (sekitar baris 187-217)
   - Ganti kode PHP logo dengan:
   ```blade
   <img src="{{ public_path('images/logo-puncak-jaya.png') }}" alt="Logo Puncak Jaya" style="width: 60px; height: 60px;">
   ```

### Opsi 2: Menggunakan Base64 PNG

1. **Convert logo ke base64:**
   ```bash
   base64 public/images/logo-puncak-jaya.png
   ```

2. **Edit template PDF:**
   - Ganti `$logoBase64` dengan:
   ```php
   $logoBase64 = 'data:image/png;base64,PASTE_BASE64_HERE';
   ```

### Opsi 3: Menggunakan SVG Custom

1. **Siapkan file SVG:**
   - File: `logo-puncak-jaya.svg` sudah tersedia sebagai template
   - Edit sesuai desain logo asli

2. **Gunakan SVG di template** (sudah diimplementasikan)

## Ukuran Logo

Logo di PDF menggunakan ukuran **60x60 pixel** agar:
- Tidak terlalu besar (hemat ruang)
- Tetap jelas dan readable
- Sesuai dengan layout 1 halaman

Jika ingin mengubah ukuran, edit di template:
```css
.kop-logo img {
    width: 60px;   /* Ubah sesuai kebutuhan */
    height: 60px;  /* Ubah sesuai kebutuhan */
}
```

## Troubleshooting

**Logo tidak muncul di PDF:**
- Pastikan path file benar
- Cek format file (PNG/SVG)
- Pastikan DomPDF support format yang digunakan

**Logo terlalu besar/kecil:**
- Edit ukuran di CSS `.kop-logo img`
- Atau edit inline style di tag `<img>`

**Logo pecah/blur:**
- Gunakan file dengan resolusi lebih tinggi
- Minimum 200x200 pixel untuk hasil terbaik

