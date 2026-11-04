# Web & Backend Service - Smart Waste Management System

> **Komponen A: Website Development & RESTful API Backend**  
> **Dibuat oleh: Adam Abdurrahman**  
> **Studi Kasus:** Smart Waste Management System  
> **Teknologi:** Laravel 12 (PHP 8.2+), Tailwind CSS, Shadcn UI Theme, Spatie Permission, Scramble OpenAPI

---

## 🔗 Ekosistem Repository Terkait & Dataset

Sistem ini merupakan bagian dari ekosistem **Smart Waste Management System** yang terbagi dalam tiga repositori terpisah:

- 🌐 **Website & Backend (Repository Ini):** [https://github.com/ADMM-ad/laravel_waste_management](https://github.com/ADMM-ad/laravel_waste_management)
- 📱 **Mobile Application (Flutter):** [https://github.com/ADMM-ad/flutter_waste_management](https://github.com/ADMM-ad/flutter_waste_management)
- 🤖 **Machine Learning Service (FastAPI):** [https://github.com/ADMM-ad/ml_waste_management](https://github.com/ADMM-ad/ml_waste_management)
- 📊 **Sumber Dataset AI:** [https://www.kaggle.com/datasets/mostafaabla/garbage-classification](https://www.kaggle.com/datasets/mostafaabla/garbage-classification)

---

## 📋 Deskripsi Proyek

Modul **Laravel Waste Management** adalah sistem inti (*backend*) yang menangani antarmuka website administratif pengawas, antarmuka web warga pelapor, serta penyedia layanan RESTful API untuk aplikasi mobile Flutter.

Sistem ini dibangun dengan mematuhi prinsip Clean Architecture (*Service-Repository Pattern*), menjamin keamanan otorisasi bertingkat, dan menyediakan jejak audit (*audit trail*) transparan untuk setiap mutasi status laporan sampah di lapangan.

---

## ✨ Fitur-Fitur Utama

1. **Autentikasi & Otorisasi Berbasis Role:**
   - Role **Admin**: Memantau statistik global kota, mengelola akun pengguna (aktif/nonaktif), mengubah status laporan, dan menghapus laporan tidak valid.
   - Role **User (Warga)**: Mengirim laporan timbunan sampah, memantau riwayat status laporan miliknya, dan mengedit/membatalkan laporan yang masih berstatus *Pending*.
2. **Dashboard Statistik Real-Time:**
   - Visualisasi total laporan, laporan selesai, laporan ditangani, dan persentase penyelesaian.
   - Filter rentang tanggal fleksibel (secara default menampilkan data *Bulan Ini*).
   - Grafik distribusi kategori sampah dan tren pelaporan harian.
3. **Manajemen Laporan Sampah (CRUD):**
   - Formulir pelaporan warga di web dilengkapi *Leaflet OpenStreetMap Interactive Pin*, pendeteksi GPS browser, upload foto, pemilihan skala volume (*Kecil, Sedang, Besar*), dan tingkat urgensi (*Rendah, Sedang, Darurat*).
   - Integrasi cerdas dengan Microservice Machine Learning (MobileNetV2) melalui proxy backend untuk mendeteksi kategori sampah secara instan tanpa isu CORS.
   - Validasi error inline interaktif dan ramah pengguna di setiap kolom input.
4. **Pembaruan Status & Riwayat Audit (Status Logs):**
   - Modal pintasan ubah status dengan *Searchable Dropdown (Tom Select)* dan keterangan makna tiap status.
   - Di dalam modal detail tetap disediakan formulir pembaruan status lengkap dengan riwayat log (*who, when, status transition, and notes*).
   - Admin dapat memperbarui status laporan kapan saja tanpa terkunci permanen.
5. **Manajemen Pengguna (User Management):**
   - Tabel direktori pengguna dengan fitur pencarian dan filter status akun.
   - Tombol aktifkan/nonaktifkan akun dilengkapi **Modal Konfirmasi Shadcn Kustom** (bebas dari *browser alert default*).
6. **Optimasi Berkas Media Otomatis:**
   - Kompresi gambar otomatis via PHP GD: resolusi di-resample maksimal 1600px dengan kompresi 80%, memastikan file tersimpan ringan di storage server dan selalu di bawah 5MB.
7. **Dokumentasi API Terintegrasi:**
   - Dokumentasi Swagger OpenAPI interaktif otomatis tersedia langsung di rute `/docs/api`.

---

## 🛠️ Persyaratan Sistem

- PHP `>= 8.2` (dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`, `sqlite3`/`pdo_mysql`, `curl`)
- Composer `>= 2.x`
- SQLite atau MySQL Database
- Node.js & NPM *(Opsional, seluruh styling telah dioptimasi via Play CDN)*

---

## 🚀 Panduan Instalasi & Konfigurasi Port Lokal

### ⚠️ Informasi Port Ekosistem Lokal
Agar seluruh komponen tidak mengalami bentrok (*port conflict*), alokasi port lokal standar adalah:
- **Laravel Backend & Web:** Port `8080` $\rightarrow$ `http://127.0.0.1:8080`
- **Machine Learning FastAPI:** Port `8000` $\rightarrow$ `http://127.0.0.1:8000`
- **Flutter Mobile:** Terhubung ke `http://10.0.2.2:8080/api/v1` (Emulator) atau `http://127.0.0.1:8080/api/v1` (Web)

---

### Langkah Menjalankan:

1. **Masuk ke direktori:**
   ```bash
   cd laravel_waste_management
   ```

2. **Pasang dependensi PHP melalui Composer:**
   ```bash
   composer install
   ```

3. **Siapkan file konfigurasi environment:**
   ```bash
   cp .env.example .env
   ```
   *Pastikan di dalam `.env` variabel URL microservice AI mengarah ke port 8000:*
   ```env
   FASTAPI_URL=http://127.0.0.1:8000
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan migrasi database dan seeder data awal:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Buat tautan simbolik untuk penyimpanan publik foto laporan:**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan web server Laravel pada Port 8080:**
   ```bash
   php artisan serve --port=8080
   ```

8. **Akses di browser:**
   - **Website Portal Admin / Warga:** [http://127.0.0.1:8080/admin/login](http://127.0.0.1:8080/admin/login)
   - **Dokumentasi REST API Swagger:** [http://127.0.0.1:8080/docs/api](http://127.0.0.1:8080/docs/api)

---

## 🔑 Akun Demo Pengujian

| Role | Username / Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` *(atau admin@example.com)* | `password123` | Akses penuh dashboard, kelola laporan, dan mutasi status |
| **Warga (User)** | `warga@example.com` | `password123` | Akses pembuatan laporan dan pelacakan status penanganan |

---

## 🧪 Pengujian Otomatis (Automated Tests)

Sistem telah dilengkapi dengan *Automated Feature & Unit Testing* yang komprehensif:

```bash
php artisan test
```

**Hasil Pengujian:**
- Total Tests: **27 Tests Passed**
- Assertions: **102 Assertions Passed**
- Status: **100% Passed (0 Errors)**

---

*Hak Cipta © 2026 Adam Abdurrahman — Smart Waste Management System.*
