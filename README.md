# UAS-PEMROGRAMAN-WEB
# Nama  : Vivit Nurul Hidayah 
# Kelas : TI.24.A.1 
# NIM : 312410110 
# Mata Kuliah : Pemrograman Web 

# ☕ Sistem Manajemen Kedai Kopi Titik Temu 


## 📋 Tentang Proyek Ini

Ini adalah proyek UAS mata kuliah Pemrograman Web untuk membuat sistem manajemen kedai kopi sederhana. Aplikasi ini memungkinkan admin mengelola produk kopi dan pelanggan melihat menu dengan fitur pencarian dan filter.

## ✨ Fitur yang Berjalan

### ✅ Sudah Bisa:
- **Halaman Utama** - Menampilkan produk kopi
- **Pencarian** - Cari produk berdasarkan nama
- **Filter Kategori** - Filter produk berdasarkan kategori
- **Pagination** - Bagi data menjadi beberapa halaman
- **Dashboard Admin** - Statistik produk sederhana
- **Login Sederhana** - Akses admin dengan username/password
- **Responsive Design** - Tampilan menyesuaikan device

### 🔧 Sedang Dikembangkan:
- CRUD produk lengkap
- Upload gambar produk
- Sistem user dengan role berbeda
- Form validation yang lebih baik

## 🚀 Instalasi Cepat

### 1. Requirements:
- XAMPP (Apache + MySQL + PHP)
- Browser modern

### 2. Setup Database:
```sql
CREATE DATABASE kedai_kopi;
USE kedai_kopi;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    stock INT,
    category VARCHAR(50)
);
```

### 3. Jalankan:
1. Copy folder ke `htdocs`
2. Start Apache & MySQL di XAMPP
3. Akses: `http://localhost/kedai-kopi-uas/`
4. Login admin: `admin` / `admin123`

## 📁 Struktur Proyek

```
KEDAI-KOPI-UAS/                     # Nama repository
│
├── 📁 APP/                         # Folder utama aplikasi
│   ├── 📁 config/
│   │   └── Database.php           # File koneksi database
│   ├── 📁 controllers/
│   │   ├── HomeController.php     # Controller halaman utama
│   │   └── AdminController.php    # Controller admin
│   ├── 📁 models/
│   │   └── Product.php            # Model produk
│   └── 📁 views/                  # (Opsional) File view
│
├── 📁 ASSETS/                      # CSS, JS, Images
│   ├── 📁 css/
│   ├── 📁 js/
│   └── 📁 images/
│       └── 📁 products/           # Gambar produk
│
├── 📁 DATABASE/                    # File SQL dan backup
│   ├── kedai_kopi.sql             # SQL structure + data
│   └── kedai_kopi_backup.sql      # Backup database
│
├── 📁 SCREENSHOTS/                 # Semua screenshot
│   ├── 01-homepage.png
│   ├── 02-search.png
│   ├── 03-filter.png
│   ├── 04-pagination.png
│   ├── 05-login.png
│   ├── 06-dashboard.png
│   ├── 07-database.png
│   ├── 08-mobile-view.png
│   └── 09-code-structure.png
│
├── 📄 index.php                    # File utama
├── 📄 .htaccess                    # URL rewriting
├── 📄 README.md                    # Dokumentasi utama
├── 📄 LICENSE                      # File license
└── 📄 .gitignore                   # Ignore unnecessary files
```

## 🎯 Fitur yang Bisa Dicoba

### Untuk Pengunjung:
1. Lihat produk di homepage
2. Cari produk tertentu
3. Filter berdasarkan kategori
4. Navigasi halaman dengan pagination

### Untuk Admin:
1. Login dengan `admin` / `admin123`
2. Lihat dashboard dengan statistik
3. Lihat daftar produk

### Screenshot Program
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/e2b7aafe-348c-421e-a366-5444dfcdbb0e" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/b1dc2f3a-68be-4c89-8fdc-3ab054b5db19" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/1fc5d4d9-2710-45aa-bcd2-942741530408" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/bd712adc-598a-4e88-83ee-573dd6c51afc" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/5db6dfcc-099e-4ffc-831a-2f59de96b2a8" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/ef41a6de-bde0-4387-9c1a-8596f0a836a1" />


## 🔧 Teknologi

- **Backend:** PHP Native
- **Frontend:** Bootstrap 5
- **Database:** MySQL
- **Pattern:** MVC (sedang diterapkan)

## 👨‍💻 Developer

**Nama:** [Vivit Nurul Hidayah]
**NIM:** [312410110]
**Kelas:** [TI.24.A.1]

Dibuat untuk memenuhi tugas UAS Pemrograman Web.

---
