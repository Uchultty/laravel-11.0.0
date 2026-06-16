# Panduan Instalasi Laravel 11 dari Nol (Laptop Baru)

Panduan ini untuk Windows menggunakan **Laragon** sebagai server lokal.

---

## LANGKAH 1 — Install Laragon

Laragon sudah include PHP, MySQL, Apache, dan phpMyAdmin sekaligus.

1. Download di: https://laragon.org/download/
   - Pilih **Laragon Full** (bukan Lite, supaya sudah include MySQL & phpMyAdmin)
2. Install seperti biasa, pilih folder install (default: `C:\laragon`)
3. Jalankan **Laragon**, klik tombol **Start All**

Laragon otomatis menyalakan Apache dan MySQL.

### Cek versi PHP di Laragon

Klik kanan ikon Laragon di taskbar → **PHP** → pastikan versi **8.2.x** atau lebih baru.

Kalau versinya masih lama, ganti lewat: klik kanan → **PHP** → klik versi yang diinginkan.

---

## LANGKAH 2 — Tambahkan PHP & Composer ke PATH

Laragon punya fitur otomatis untuk ini.

Buka Laragon → klik **Menu** → **Preferences** → centang:
- **Add Laragon bin to PATH**
- **Add PHP to PATH**

Klik **Save**, lalu **restart Laragon**.

### Verifikasi di terminal

Buka **PowerShell** atau **Command Prompt** baru:

```
php -v
```

Harusnya muncul:
```
PHP 8.2.x (cli) ...
```

```
composer -V
```

Harusnya muncul:
```
Composer version 2.x.x ...
```

> Kalau Composer belum ada, download manual di: https://getcomposer.org/Composer-Setup.exe

---

## LANGKAH 3 — Install Node.js & NPM

Dibutuhkan untuk mengompilasi CSS dan JavaScript (Vite).

1. Download di: https://nodejs.org/en/download
   - Pilih **LTS** (versi stabil)
2. Install seperti biasa, ikuti wizard sampai selesai

### Verifikasi

```
node -v
npm -v
```

---

## LANGKAH 4 — Clone / Salin Project Ini

### Jika dapat dari Git:

```
git clone <URL_REPO> laravel-11.0.0
cd laravel-11.0.0
```

Disarankan taruh project di folder `C:\laragon\www\` supaya bisa diakses lewat virtual host Laragon.

### Jika dapat dari folder ZIP / copy-paste:

Ekstrak ke `C:\laragon\www\laravel-11.0.0\`, lalu buka terminal di folder tersebut.

> **Cara buka terminal di folder:** klik kanan di dalam folder → "Open PowerShell window here"

---

## LANGKAH 5 — Install Dependensi PHP

Di dalam folder project, jalankan:

```
composer install
```

Ini men-download semua library PHP ke folder `vendor/`. Tunggu sampai selesai.

---

## LANGKAH 6 — Buat File `.env`

```
copy .env.example .env
```

---

## LANGKAH 7 — Generate Application Key

```
php artisan key:generate
```

---

## LANGKAH 8 — Buat Database di phpMyAdmin

1. Buka browser, akses: **http://localhost/phpmyadmin**
2. Login dengan:
   - Username: `root`
   - Password: *(kosong, langsung klik Go)*
3. Klik tab **Databases**
4. Di kolom "Create database", ketik nama database misalnya: `laravel_db`
5. Pilih collation: `utf8mb4_unicode_ci`
6. Klik **Create**

---

## LANGKAH 9 — Konfigurasi `.env` untuk MySQL

Buka file `.env`, ubah bagian database dari SQLite ke MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

> Sesuaikan `DB_DATABASE` dengan nama database yang dibuat di Langkah 8.
> Password default Laragon biasanya kosong.

---

## LANGKAH 10 — Jalankan Migrasi

```
php artisan migrate
```

Perintah ini membuat semua tabel di database. Cek hasilnya di phpMyAdmin, tabel-tabel baru akan muncul.

---

## LANGKAH 11 — Install Dependensi JavaScript

```
npm install
```

---

## LANGKAH 12 — Jalankan Aplikasi

Buka **dua terminal** di folder project secara bersamaan:

**Terminal 1 — Laravel server:**
```
php artisan serve
```

**Terminal 2 — Vite (kompilasi CSS/JS):**
```
npm run dev
```

Buka browser, akses: **http://localhost:8000**

### Alternatif: akses lewat Laragon virtual host

Kalau project ada di `C:\laragon\www\laravel-11.0.0\`, bisa langsung akses:
**http://laravel-11.0.0.test**

Tapi tetap perlu jalankan `npm run dev` di terminal.

---

## Ringkasan Perintah (setiap kali mau kerja)

```
php artisan serve
npm run dev
```

---

## Troubleshooting

### Error: `php` atau `composer` tidak dikenali
→ Pastikan Laragon sudah running dan PATH sudah diset (Langkah 2). Restart terminal.

### Error koneksi database: `Connection refused` atau `Access denied`
→ Pastikan Laragon **Start All** sudah ditekan (MySQL harus running).
→ Cek lagi `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` di file `.env`.

### Database tidak ada / belum dibuat
→ Buat dulu lewat phpMyAdmin (Langkah 8), baru jalankan `php artisan migrate`.

### Error saat `composer install`: extension tidak ada
→ Buka Laragon → klik kanan → **PHP** → **PHP Extensions** → centang extension yang diminta (misalnya `pdo_mysql`, `zip`, `mbstring`).

### Port 8000 sudah dipakai
→ `php artisan serve --port=8080`

### Muncul halaman blank / error CSS tidak muncul
→ Pastikan `npm run dev` sedang berjalan di terminal kedua.

### Tabel tidak muncul di phpMyAdmin setelah migrate
→ Refresh halaman phpMyAdmin (F5), atau pilih database-nya dulu di panel kiri.

---

## Spesifikasi Project Ini

| Item | Detail |
|------|--------|
| Laravel | 11.x |
| PHP minimal | 8.2 |
| Server lokal | Laragon |
| Database | MySQL (via phpMyAdmin) |
| Auth | Laravel Breeze |
| PDF | mPDF |
| Timezone | Asia/Jakarta |
