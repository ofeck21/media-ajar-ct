# 🕌 CT Quest: Misi Safari Mengaji

**CT Quest: Misi Safari Mengaji** adalah media pembelajaran interaktif berbasis aplikasi web yang dirancang khusus untuk meningkatkan kemampuan **Berpikir Komputasional (*Computational Thinking* / CT)** siswa SMP (Fase D). 

Diangkat dari latar belakang dan rutinitas santri di kompleks pondok pesantren, aplikasi ini menggabungkan game simulasi labirin logika 2D dengan pendekatan pembelajaran modern Kurikulum Merdeka: **Understanding by Design (UbD)**, **Teaching at The Right Level (TaRL)**, **Culturally Responsive Teaching (CRT)**, dan **Pembelajaran Sosial Emosional (PSE)**.

---

## ✨ Fitur Utama

### 👨‍🏫 Panel Guru (Teacher Dashboard)
* **Manajemen Kelas & Siswa:** Buat kelas, kelola data siswa, dan generate akun/NISN otomatis.
* **Kontrol Sesi Permainan Interaktif:** Atur durasi permainan, pemicu (*trigger*) tes diagnostik, pengelompokan, hingga mulai/selesai sesi secara *real-time*.
* **Auto-Grouping berbasis TaRL (Homogen & Heterogen):** Pengelompokan siswa otomatis berdasarkan hasil tes diagnostik awal.
* **Manual Override Drag & Drop:** Fleksibilitas bagi guru untuk menggeser/menyesuaikan susunan anggota kelompok antar-kartu secara interaktif.
* **Live Dashboard & Leaderboard:** Pantauan progres, perolehan bintang, dan klasemen peringkat (kelompok & individu) secara *real-time* berbasis *Livewire Polling* (100% kompatibel dengan cPanel/shared hosting).

### 🎓 Panel Siswa & Gameplay
* **Pre-Test Diagnostik Singkat:** 3 soal logika untuk menentukan tingkat kesiapan belajar (*Basic*, *Intermediate*, *Advanced*).
* **Gim Labirin Logika 2D (HTML5 Canvas Engine):**
  * **Peta Tematik Pesantren:** Rute dari Asrama $\rightarrow$ Tempat Wudu $\rightarrow$ Rak Kitab $\rightarrow$ Rintangan (Jemuran/Antrean) $\rightarrow$ Masjid.
  * **Palette Blok Perintah (Algoritma):** *Maju, Belok Kiri, Belok Kanan, Ambil Wudu, Ambil Kitab, dan Looping (Perulangan)*.
  * **Interactive Debugging:** Feedback dan instruksi saran saat bot menabrak rintangan atau salah urutan langkah.
  * **Sistem Penilaian Bintang (Optimasi):** Penilaian efisiensi langkah (⭐⭐⭐) untuk melatih abstraksi dan optimasi algoritma.
* **Refleksi Metakognitif & PSE:** *Emotional Check-in* (pemilihan emotikon perasaan) dan refleksi 4 pilar CT setelah permainan selesai.

### 👑 Panel Superadmin
* **Manajemen Data Master:** Kelola data sekolah/pesantren, verifikasi akun guru, dan monitoring aktivitas sesi permainan secara global.

---

## 🛠️ Tech Stack

* **Backend Framework:** Laravel 13 (PHP 8.3+)
* **Database:** SQLite (default) / MySQL / PostgreSQL
* **Authentication:** Laravel Fortify
* **Frontend UI:** Livewire Flux & Tailwind CSS
* **Drag & Drop Component:** SortableJS / Native HTML5 Drag & Drop
* **Live Sync Dashboard:** Livewire 4 (`wire:poll`)
* **Game Engine:** Vanilla JavaScript (HTML5 Canvas API)

---

## 🚀 Fitur Integrasi Pembelajaran (PPG Alignment)

1. **Culturally Responsive Teaching (CRT):** Konteks masalah diangkat dari kehidupan sehari-hari santri (berkemas kitab, wudu, dan menuju masjid tepat waktu).
2. **Teaching at The Right Level (TaRL):** Pembagian tingkat kesulitan labirin dan pengelompokan berdasarkan hasil asesmen diagnostik.
3. **Pembelajaran Sosial Emosional (PSE):** Fitur latihan pernapasan/mindfulness sebelum belajar dan form refleksi kesadaran diri (*Self-Awareness*) serta pengelolaan diri (*Resilience*) saat *debugging*.

---

## 📋 Persyaratan Sistem

* PHP >= 8.3
* Composer >= 2.x
* Node.js & NPM

---

## 💻 Instalasi & Menjalankan Aplikasi Lokal

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek secara lokal:

1. **Clone repositori ini:**
   ```bash
   git clone <URL_REPOSITORY_ANDA>
   cd game-media-ajar
   ```

2. **Instal dependensi PHP (Composer):**
   ```bash
   composer install
   ```

3. **Instal dependensi JavaScript (NPM):**
   ```bash
   npm install
   ```

4. **Siapkan konfigurasi `.env`:**
   Salin file `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Konfigurasi Database:**
   Secara default, Laravel 11+ menggunakan SQLite. Jika Anda ingin menggunakan database MySQL atau PostgreSQL, sesuaikan koneksi `DB_CONNECTION` pada file `.env`. 
   
   Jalankan migrasi beserta *seeder* awal (untuk membuat akun *superadmin*, dsb):
   ```bash
   php artisan migrate --seed
   ```

7. **Build Aset Frontend (Tailwind/Flux):**
   ```bash
   npm run build
   # atau untuk mode development: npm run dev
   ```

8. **Jalankan Development Server:**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses melalui browser di alamat: `http://localhost:8000`

---

## 👥 Hak Akses (Roles) Default

Saat menjalankan `php artisan migrate --seed`, sistem akan membuatkan beberapa akun utama:
- **Superadmin:** `superadmin@ctquest.test` / Password: `password`
- **Guru:** Dapat didaftarkan oleh Superadmin atau melalui halaman registrasi.
- **Siswa:** Dibuat otomatis (*generate*) oleh Guru di dalam pengaturan Kelas.

---

## 📄 Lisensi
CT Quest dikembangkan untuk keperluan pendidikan dan dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT). Silakan modifikasi dan gunakan secara bebas untuk kemajuan pendidikan di Indonesia.
