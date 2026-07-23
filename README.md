# 🚀 Automation Get Data - YouTube Shorts Automation Platform

Sistem otomatisasi berbasis web & Playwright untuk melakukan **auto-scroll** dan **scraping data YouTube Shorts** (Judul, Channel, URL, dan Timestamp) secara terstruktur, terintegrasi dengan Backend API Laravel & Queue Handler.

![Tech Stack](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python&logoColor=white)
![Playwright](https://img.shields.io/badge/Playwright-Automation-2EAD33?style=for-the-badge&logo=playwright&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
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
- **Python** >= 3.10
- **Docker & Docker Compose** (Opsional untuk deployment cepat)
- **Database**: MySQL / MariaDB

---

## 🐳 Opsi 1: Menjalankan via Docker (Satu Perintah)

Seluruh service (MySQL Database, Backend API Laravel, dan Python Playwright Service) dapat dijalankan sekaligus dengan 1 perintah:

```bash
docker-compose up -d --build
```
- Web Application & API: `http://localhost:8000`
- Database MySQL: `localhost:3306`

---

## ⚙️ Opsi 2: Panduan Instalasi & Menjalankan Lokal (Tanpa Docker)

### 1. Clone Repository
```bash
git clone https://github.com/IntanBudiarty/automation-get-data.git
cd automation-get-data
```

### 2. Setup Backend (Laravel)
```bash
cd BackendAutomation
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3. Setup Python Service (Playwright)
```bash
cd ../Automation
python -m venv venv
# Windows: venv\Scripts\activate | Linux/macOS: source venv/bin/activate
pip install -r requirements.txt
playwright install chromium
```

### 4. Jalankan Server Lokal
```bash
cd ../BackendAutomation
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
| `POST` | `/api/automation/callback` | Webhook simpan hasil scraping Playwright | Publik |
| `GET` | `/api/histories` | Mengambil daftar riwayat (Support pagination & filter) | Bearer Token |
| `GET` | `/api/histories/{id}` | Detail video yang discrape dalam satu job | Bearer Token |

---

## 🎁 Poin Bonus & Dokumentasi Postman

File koleksi Postman untuk pengujian API telah disediakan pada repository:
- **File Postman Collection**: `Automation Get Data API.postman_collection.json`

Dapat langsung di-import ke aplikasi Postman untuk menguji endpoint Register, Login, Refresh Token, Start Automation Job, dan History API.

---

## 👤 Penulis & Lisensi

- **Developer**: Intan Budiarty
- **Repository**: [automation-get-data](https://github.com/IntanBudiarty/automation-get-data)
- **Lisensi**: MIT License
