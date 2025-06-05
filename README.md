# Todo-List-MVC-App---PHP-MySQL

Aplikasi Todo List sederhana menggunakan PHP dan MySQL dengan arsitektur MVC (Model-View-Controller). Proyek ini dikembangkan sebagai bagian dari Praktikum Mata Kuliah **Pengembangan Aplikasi Berbasis Web (PABWE)** di Institut Teknologi Del. Aplikasi ini juga telah ditambahkan fitur **Edit** dan **Hapus** todo item.


## 🛠️ Fitur Utama

- ✅ Tambah aktivitas
- 📝 Edit aktivitas dan status (Selesai/Belum)
- ❌ Hapus aktivitas
- 📄 Penyimpanan data menggunakan MySQL
- 🎨 Tampilan antarmuka menggunakan Bootstrap 5
- 📂 Arsitektur proyek menggunakan pendekatan MVC

---

## 📦 Teknologi yang Digunakan

| Teknologi       | Keterangan                                  |
|----------------|----------------------------------------------|
| PHP 8+         | Bahasa server-side utama                     |
| MySQL (MariaDB)| Database relasional                          |
| Bootstrap 5    | Library CSS untuk UI responsif               |
| SQLyog         | GUI untuk manajemen database                 |
| Composer       | Dependency manager untuk PHP (opsional)      |

---

## 📁 Struktur Proyek

todo-mvc-php-pabwe/
│
├── config.php
├── controllers/
│ └── TodoController.php
├── models/
│ └── Todo.php
├── views/
│ └── index.php
├── public/
│ └── index.php
├── assets/
│ └── vendor/ (Bootstrap)

## ⚙️ Instalasi dan Menjalankan Proyek

1. Clone repository:

   ```bash
   git clone https://github.com/username/todo-mvc-php-pabwe.git
   cd todo-mvc-php-pabwe

##  Buat database baru di SQLyog / phpMyAdmin:

sql
Copy
Edit
CREATE DATABASE pabwe_praktikum7;
USE pabwe_praktikum7;

CREATE TABLE todo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity VARCHAR(250) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

## Jalankan server lokal:

php -S localhost:8000 -t public


