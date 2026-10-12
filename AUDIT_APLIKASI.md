# Hasil pengujian APIP Manager

Tanggal: 7 September 2026. Lingkungan: PHP 8.5.0, Node 23.11.0, SQLite in-memory untuk tes integrasi.

## Status perbaikan — 9 September 2026

Perbaikan kode dan migrasi database lokal sudah diterapkan. Bagian audit 7 September di bawah dipertahankan sebagai catatan kondisi sebelum perbaikan.

### Perbaikan yang selesai

- API pegawai lokal menyediakan daftar/detail dari direktori eksternal. Rute controller yang hilang dipulihkan sebagai akses baca; mutasi pegawai dilakukan di aplikasi sumber, endpoint mutasi lokal mengembalikan 405. Kontrak terbaru dijelaskan di API_DOCUMENTATION.md.
- Web dan API pengawasan memakai PersonilService untuk membaca dan menyimpan snapshot. Anggota diambil dengan query batch; snapshot nama/personil tetap tersedia saat layanan eksternal offline.
- Create/update API berjalan dalam transaksi sampai serialisasi respons; kegagalan child write atau validasi personil membatalkan seluruh perubahan. Penyimpanan snapshot web juga dipindahkan sebelum commit.
- Validasi nomor ST unik, batas pagination, duplikasi personil, konflik ketua/anggota, serta persyaratan laporan sebelum selesai diterapkan. Foreign key ke direktori pegawai lokal dilepas agar ID eksternal valid dapat digunakan, sementara relasi parent-child pengawasan dipertahankan. Nomor ST web boleh kosong untuk draft.
- Dashboard menangani kegagalan koneksi tanpa 500 dan menampilkan peringatan; pencarian/API mengembalikan 503 saat upstream gagal. Timeout koneksi 3 detik dan request 10 detik. Pengambilan direktori mengikuti halaman berikutnya dan mendeteksi pagination yang tidak bergerak; hasil digunakan ulang dalam satu request.
- Pemeriksaan konflik hanya membaca pengawasan yang melibatkan personil terkait, menyaring tanggal awal, dan tidak lagi menjalankan query pivot per record. Ditambahkan indeks personil/tanggal.
- Pengaturan dibaca sekali dalam satu request dan invalidasi terjadi setelah perubahan model. Simpan pengaturan dilakukan dalam transaksi.
- Ekspor membaca pengawasan bertahap dan memakai response download Laravel. Batas bawaan 1.000 penugasan/2.000 baris di config/pengawasan.php; hasil lebih besar meminta filter yang lebih sempit. NIP dan teks bebas disimpan sebagai string agar tidak berubah menjadi angka/formula.
- Unggahan laporan memakai nama unik; kegagalan update DB membersihkan file baru dan mempertahankan laporan lama. Penghapusan relasi pengawasan menggunakan transaksi.
- Login dibatasi 5 percobaan/menit per email+IP dan 30/menit per IP. Logout web hanya POST. Deklarasi template laporan ganda sudah dihapus.
- Kolom snapshot penandatangan/kop ditambahkan ke fillable agar benar-benar tersimpan; API baru juga menyimpan snapshot pengaturan dokumen.

### Verifikasi akhir

- `php artisan test`: **24 tes lulus, 114 assertions**, tanpa tes risky (22 tes audit/regresi dan 2 tes bawaan).
- `npm run build`: berhasil; CSS gzip 12,01 kB, JS gzip 18,58 kB.
- `php artisan route:list --except-vendor`: berhasil, 37 rute.
- `git diff --check`: bersih; file PHP yang diubah telah diformat menggunakan Pint.
- Tes mencakup create/read/update/delete API, rollback, ID eksternal dengan foreign key aktif, snapshot offline, batas tanggal bentrok, pagination upstream, pembatasan login, kegagalan unggah laporan, isi workbook Excel, dan keluaran surat tugas berformat PDF.
- Pemetaan 30 pengawasan diuji menggunakan maksimal 2 query DB dan 1 request HTTP dengan direktori fixture satu halaman.
- Migrasi diuji pada salinan database, termasuk rollback/maju kembali, lalu diterapkan ke database lokal. `PRAGMA integrity_check` menghasilkan `ok`, tanpa pelanggaran foreign key. Jumlah record sebelum/sesudah tidak berubah.
- Backup sebelum migrasi: `storage/app/private/backups/apip-before-fixes-20260909.sqlite` (lokal, tidak masuk Git). Rollback otomatis ditolak jika schema lama akan membatalkan draft atau referensi ID eksternal baru; pemulihan penuh menggunakan backup harus mempertimbangkan data yang dibuat setelah backup.

### Batas dan tindak lanjut

- Layanan pegawai dalam tes disimulasikan; integrasi upstream sebenarnya, UI browser/mobile, dan beban pengguna bersamaan belum diverifikasi. Tes PDF memeriksa dokumen dapat dihasilkan, bukan inspeksi visual seluruh format cetakan.
- Ekspor besar dibatasi; belum dibuat job antrean untuk ekspor di atas batas. Cache pengaturan/direktori berlaku per request, bukan cache lintas request.
- Hak akses administratif tetap mengikuti perilaku aplikasi yang ada. Penetapan admin tertentu belum dilakukan karena daftar akun/aturan admin belum diberikan.
- Tidak ada deployment server atau commit Git yang dilakukan.

---

## Ringkasan pengujian

- `npm run build`: berhasil, Vite 7.3.6; CSS 71,79 kB (gzip 11,94 kB), JS 48,62 kB (gzip 18,58 kB). Angka ini ukuran bundle, bukan hasil pengukuran kecepatan halaman.
- `php artisan test` sebelum penambahan tes audit: 2 tes bawaan lulus, 2 assertions. Cakupannya halaman depan dan assertion true.
- `php artisan test --filter=ApplicationAuditTest`: 5 gagal, 1 lulus, 1 risky, 14 assertions.
- `php artisan route:list --except-vendor`: gagal karena PegawaiApiController tidak ditemukan.
- Migrasi database dijalankan oleh RefreshDatabase pada SQLite in-memory dalam tes audit. Database aplikasi tidak dimigrasikan atau diisi data pengujian.
- Tes HTTP menggunakan kernel Laravel; koneksi API pegawai disimulasikan dan request eksternal yang tidak disimulasikan dicegah.

## Masalah terkonfirmasi dan prioritas

### P1 — API pegawai tidak dapat digunakan

Lokasi: routes/api.php:39. Rute resource mengarah ke `App\Http\Controllers\Api\PegawaiApiController`, tetapi file controller tersebut tidak ada. GET `/api/pegawai` dengan pengguna terautentikasi menghasilkan HTTP 500. Daftar rute artisan juga gagal.

Perbaikan: implementasikan controller sesuai kontrak API yang dibutuhkan, atau hapus rute yang sudah tidak digunakan setelah memeriksa konsumennya. Jangan sekadar membuat controller kosong.

### P1 — API pengawasan memakai relasi yang sudah tidak tersedia

Lokasi: app/Http/Controllers/Api/PengawasanApiController.php:18 dan pemanggilan `load()` pada store/update. Model app/Models/Pengawasan.php hanya menyediakan relasi dasarHukum, sedangkan API memuat penanggungJawab, pengendaliTeknis, ketuaTim, anggota.

GET `/api/pengawasan` ketika ada satu data menghasilkan HTTP 500 dengan `RelationNotFoundException`. POST payload valid juga menghasilkan HTTP 500. Jalur web sudah memakai snapshot/API pegawai, sementara API masih memakai rancangan relasi sebelumnya.

Perbaikan: satukan penyelesaian data personil dalam service yang dipakai web dan API; gunakan snapshot historis secara konsisten dan sesuaikan resource response. Uji daftar berisi data, detail, create, dan update.

### P1 — Respons gagal masih meninggalkan data tersimpan

Lokasi: app/Http/Controllers/Api/PengawasanApiController.php, metode store/update.

Tes POST valid menerima 500 tetapi tabel pengawasan tetap berisi satu baris. Pembuatan record, anggota, dasar hukum, dan pemuatan data respons tidak dilindungi transaksi. Klien dapat mengira penyimpanan gagal lalu mengulang request, sementara nomor ST pertama sudah tersimpan.

Perbaikan: bungkus mutasi terkait dalam transaksi, pastikan penyiapan respons yang dapat gagal ditangani sebelum commit, dan validasi keunikan nomor ST dengan respons 422. Tambahkan tes rollback saat operasi anak gagal serta konsistensi update. Jangan tampilkan pesan exception internal ke klien.

### P1 — Dashboard gagal ketika layanan pegawai tidak terhubung

Lokasi: app/Http/Controllers/AuthController.php:54. ConnectionException yang disimulasikan menghasilkan HTTP 500 pada `/dashboard`.

Perbaikan: tangani connection timeout/error, tetapkan timeout koneksi dan total secara eksplisit, tampilkan status layanan tidak tersedia, dan gunakan cache terakhir bila tersedia. Terapkan pola serupa di PegawaiController dan PengawasanController. Bedakan data kosong dari kegagalan layanan; searchApi saat ini mengembalikan data kosong berstatus 200 pada respons upstream gagal.

## Optimalisasi berdasarkan peninjauan kode

Temuan berikut belum diukur menggunakan load test produksi.

1. **P2 — Pemeriksaan bentrok membaca seluruh pengawasan.** PengawasanController.php:879, khususnya query `get()` pada baris 890, memuat semua record untuk setiap personil yang diperiksa dan menjalankan query anggota pada record yang overlap. Saring tanggal dan personil di database, gunakan exists/join, serta evaluasi indeks setelah bentuk query final. Uji dengan jumlah data dan personil yang realistis.
2. **P2 — Pengambilan pegawai dibatasi 1.000 data.** injectPegawaiData, create, edit, dan getPegawaiNameFromApi mengambil satu halaman `per_page=1000`. Pegawai di luar halaman tersebut dapat tidak terpetakan; pembacaan pivot per pengawasan tanpa snapshot juga menambah query. Ambil hanya ID yang diperlukan jika upstream mendukung, atau ikuti pagination; batch pivot dan cache respons yang sesuai.
3. **P2 — Ekspor memuat seluruh hasil ke memori.** RekapController.php:51 memakai get(), lalu membuat seluruh workbook. Gunakan batas rentang ekspor, pemrosesan bertahap dan job queue untuk volume besar; ukur penggunaan memori. Chunk database saja tidak menghilangkan penggunaan memori workbook.
4. **P2 — Pengaturan dibaca berulang.** SettingController.php:14–26 menjalankan 11 query key terpisah; AppServiceProvider juga membaca settings pada boot. Ambil sekaligus dengan pluck dan cache dengan invalidasi setelah perubahan.
5. **P2 — Proteksi akses perlu diperjelas.** Rute pengaturan sistem dan mutasi web hanya memakai auth, tanpa policy/gate yang terlihat. Jika ada pengguna non-admin, batasi akses pengaturan URL/token API dan operasi administratif. Route login web/API tidak memiliki throttle eksplisit; tambahkan pembatas percobaan login. GET logout juga sebaiknya diganti POST dengan CSRF dan tautan UI disesuaikan.
6. **P2 — Kontrak validasi web/API berbeda.** API memvalidasi personil terhadap tabel pegawai lokal meskipun web menggunakan API eksternal. Satukan sumber kebenaran personil dan aturan bisnis, termasuk bentrok jadwal dan persyaratan laporan sebelum selesai.

## Cakupan yang berhasil dan batas hasil

- Pengguna tamu diarahkan dari dashboard ke login.
- Dengan respons pegawai kosong yang valid, dashboard, pegawai, daftar pengawasan, form pengawasan, laporan, rekap, dan setting mengembalikan HTTP 200. Tes gabungan ini berstatus risky karena output buffer tidak ditutup; belum layak disebut lulus bersih. Akar kebocoran buffer belum diisolasi.
- Belum melakukan pengujian interaksi browser, tampilan mobile, unggah laporan, verifikasi hasil PDF/XLSX, beban pengguna bersamaan, atau integrasi layanan pegawai sebenarnya.
- Belum mengaudit kerentanan versi dependency terhadap database advisory terbaru.
- Hasil ini tidak membuktikan aplikasi siap produksi atau memberi angka kapasitas maksimum.

## Urutan pengerjaan

1. Pulihkan API pegawai dan pengawasan; samakan sumber data personil.
2. Perbaiki transaksi penyimpanan dan ketahanan gangguan layanan pegawai.
3. Pastikan aturan hak akses, throttle login, dan validasi bisnis konsisten.
4. Optimalkan query, pagination pegawai, cache, dan ekspor berdasarkan pengukuran.
5. Perluas regresi CRUD, konflik jadwal, unggah file, cetak/ekspor, lalu uji browser/mobile dan beban.

Tes reproduksi tersedia di tests/Feature/ApplicationAuditTest.php. Tes tersebut sengaja memeriksa perilaku yang seharusnya benar dan saat ini mendeteksi bug; belum dilakukan perubahan kode aplikasi untuk memperbaikinya.
