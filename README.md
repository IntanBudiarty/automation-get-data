# 🚀 Automation Get Data - YouTube Shorts Automation Platform

Sistem otomatisasi berbasis web & Playwright untuk melakukan **auto-scroll** dan **scraping data YouTube Shorts** (Judul, Channel, URL, dan Timestamp) secara terstruktur, terintegrasi dengan Backend API Laravel & Queue Handler.

![Tech Stack](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python&logoColor=white)
![Playwright](https://img.shields.io/badge/Playwright-Automation-2EAD33?style=for-the-badge&logo=playwright&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## 📌 Arsitektur & Workflow Sistem

```
┌─────────────────┐      HTTP REST      ┌─────────────────┐
│                 │ ─────────────────>  │                 │
│  Blade Frontend │                     │  Backend API    │
│  (Bootstrap 5)  │  <───────────────── │  (Laravel 11)   │
└─────────────────┘      JSON Token     └────────┬────────┘
                                                 │
                                           Dispatch Job
                                                 │
                                                 ▼
┌─────────────────┐     HTTP Callback   ┌─────────────────┐
│                 │ <─────────────────  │                 │
│ Database MySQL  │                     │ Python Engine   │
│ (Histories/Vids)│ <─────────────────  │  (Playwright)   │
└─────────────────┘      Save Data      └─────────────────┘
```

1. **Frontend (Laravel Blade & Bootstrap 5)**: Menyediakan antarmuka untuk registrasi, login, submit durasi scroll, dan melihat riwayat data video discrape dalam bentuk modal DataTable.
2. **Backend API (Laravel 11 & Sanctum)**: Menangani otentikasi Sanctum (access token & refresh token), validasi input, queue handler, dan endpoint REST API.
3. **Python Automation Service (Playwright)**: Membuka browser Chromium headless/headful, melakukan simulasi scroll YouTube Shorts sesuai durasi, dan mengirimkan hasil scraping ke Laravel via callback.

---

## 🛠️ Spesifikasi & Syarat Sistem

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.0 & **NPM**
- **Python** >= 3.10
- **Database**: MySQL / MariaDB (via Laragon / XAMPP / Native)

---

## ⚙️ Panduan Instalasi & Cara Menjalankan

### 1. Clone Repository
```bash
git clone https://github.com/IntanBudiarty/automation-get-data.git
cd automation-get-data
```

---

### 2. Setup Backend (Laravel)

Masuk ke folder `BackendAutomation`:
```bash
cd BackendAutomation
```

1. **Install Dependensi PHP**:
   ```bash
   composer install
   ```

2. **Setup File Environment (.env)**:
   Salin `.env.example` menjadi `.env` (atau buat file `.env` baru):
   ```bash
   cp .env.example .env
   ```
   Pastikan konfigurasi database di file `.env` sudah benar:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=backendautomation
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

4. **Jalankan Migrasi Database**:
   ```bash
   php artisan migrate
   ```
*(Catatan: Frontend menggunakan Blade Template dengan CDN langsung, sehingga tidak memerlukan langkah `npm install` atau bundler Node.js).*

---

### 3. Setup Python Service (Playwright)

Masuk ke folder `Automation` (di luar `BackendAutomation`):
```bash
cd ../Automation
```

1. **Buat & Aktifkan Virtual Environment Python**:
   - **Windows (Command Prompt / PowerShell)**:
     ```bash
     python -m venv venv
     venv\Scripts\activate
     ```
   - **Linux / macOS**:
     ```bash
     python3 -m venv venv
     source venv/bin/activate
     ```

2. **Install Playwright & Dependensi Python**:
   ```bash
   pip install playwright
   playwright install chromium
   ```

---

### 4. Menjalankan Server Lokal

Untuk menjalankan seluruh sistem, jalankan server Laravel:

```bash
cd BackendAutomation
php artisan serve
```
*Server Laravel akan berjalan di `http://127.0.0.1:8000`.*

---

## 🌐 Endpoint REST API

### 🔑 Autentikasi
| Method | Endpoint | Deskripsi | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Pendaftaran Akun Baru | Publik |
| `POST` | `/api/login` | Login & Merilis Pair Token | Publik |
| `POST` | `/api/refresh` | Menerbitkan Access Token Baru | Publik |
| `POST` | `/api/logout` | Revoke/Invalidasi Token | Bearer Token |

### ⚙️ Automation & History
| Method | Endpoint | Deskripsi | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/automation/start` | Submit Job Scroll (Payload: `{"duration": 30}`) | Bearer Token |
| `POST` | `/api/automation/callback` | Webhook simpan hasil scraping Playwright | Publik / Secret |
| `GET` | `/api/histories` | Mengambil daftar riwayat (Support pagination & filter) | Bearer Token |
| `GET` | `/api/histories/{id}` | Detail video yang discrape dalam satu job | Bearer Token |

---

## 🖥️ Halaman Web Frontend

Akses URL berikut pada browser setelah server berjalan:
- **Dashboard App**: [http://127.0.0.1:8000/dashboard](http://127.0.0.1:8000/dashboard)
- **Login Akun**: [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
- **Daftar Akun**: [http://127.0.0.1:8000/register](http://127.0.0.1:8000/register)

---

## 👤 Penulis & Lisensi

- **Developer**: Intan Budiarty
- **Repository**: [automation-get-data](https://github.com/IntanBudiarty/automation-get-data)
- **Lisensi**: MIT License
