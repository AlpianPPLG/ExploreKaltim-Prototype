# 📅 COMPREHENSIVE PROJECT PLAN: EXPLORE KALTIM

## 1. 📝 Ringkasan Eksekutif (Executive Summary)

**Explore Kaltim** adalah platform web modern dan komprehensif yang dirancang untuk mempromosikan pariwisata Kalimantan Timur. Platform ini tidak hanya berfungsi sebagai landing page informasi, tetapi juga berevolusi menjadi sistem informasi pariwisata yang interaktif, memungkinkan pengguna untuk menjelajahi destinasi, melihat event, memberikan ulasan, dan merencanakan kunjungan.

Tujuan utama proyek ini adalah memperkenalkan keindahan "Benua Etam" (Kalimantan Timur) ke mata dunia dengan pengalaman pengguna (User Experience) yang premium, visual yang memukau, dan data yang terstruktur.

---

## 2. 🏗️ Arsitektur Sistem & Teknologi

Untuk mencapai performa tinggi, kemudahan maintenance, dan skalabilitas, proyek ini menggunakan *Technology Stack* berikut:

### 2.1. Frontend (User Interface)
*   **HTML5 & CSS3**: Struktur semantik dan styling modern.
*   **Tailwind CSS**: Framework utility-first untuk desain responsif, cepat, dan konsisten.
*   **Vanilla JavaScript (ES6+)**: Logika interaktif ringan tanpa overhead framework berat di sisi klien untuk performa maksimal (atau opsional menggunakan React/Vue jika kompleksitas meningkat).
*   **Libraries**: AOS (Animate On Scroll), SwiperJS (Carousel), Lightbox (Gallery).

### 2.2. Backend (Server & API)
*   **Node.js & Express.js**: Runtime environment yang cepat dan scalable untuk menangani request API.
*   **RESTful API**: Arsitektur komunikasi data yang standar.
*   **JWT (JSON Web Token)**: Untuk keamanan autentikasi (Login/Register).
*   **Multer**: Middleware untuk menangani upload file (gambar destinasi).

### 2.3. Database
*   **MySQL**: Relational Database Management System (RDBMS) untuk menyimpan data terstruktur dengan relasi yang kuat.

---

## 3. 💾 Desain Database (MySQL Schema)

Sesuai kebutuhan sistem yang kompleks dan terstruktur, berikut adalah rancangan database dengan minimal **8 tabel utama** yang saling berelasi.

### Diagram ERD Singkat (Konsep)
`Users` melakukan `Reviews` pada `Destinations`.
`Destinations` berada di `Regencies` dan memiliki `Categories`.
`Destinations` memiliki banyak `Destination_Galleries`.
`Destinations` bisa memiliki banyak `Events`.
`Inquiries` masuk dari publik.

### Detail Tabel Database

#### 1. Table: `users`
Menyimpan data pengguna (admin dan pengunjung terdaftar).
*   `id` (INT, PK, Auto Increment)
*   `username` (VARCHAR(50), Unique)
*   `email` (VARCHAR(100), Unique)
*   `password_hash` (VARCHAR(255))
*   `role` (ENUM('admin', 'user'), Default: 'user')
*   `avatar_url` (VARCHAR(255))
*   `created_at` (TIMESTAMP)

#### 2. Table: `regencies` (Kabupaten/Kota)
Data administratif daerah di Kalimantan Timur (contoh: Samarinda, Balikpapan, Berau).
*   `id` (INT, PK, Auto Increment)
*   `name` (VARCHAR(100)) - Contoh: "Kabupaten Berau"
*   `code` (VARCHAR(10)) - Kode unik area
*   `description` (TEXT) - Deskripsi singkat daerah
*   `thumbnail_url` (VARCHAR(255))

#### 3. Table: `categories`
Pengelompokan jenis wisata.
*   `id` (INT, PK, Auto Increment)
*   `name` (VARCHAR(50)) - Contoh: "Wisata Bahari", "Budaya", "Kuliner"
*   `slug` (VARCHAR(50), Unique)
*   `icon_class` (VARCHAR(50)) - Untuk ikon frontend

#### 4. Table: `destinations`
Tabel inti yang menyimpan data tempat wisata.
*   `id` (INT, PK, Auto Increment)
*   `regency_id` (INT, FK -> regencies.id)
*   `category_id` (INT, FK -> categories.id)
*   `name` (VARCHAR(150))
*   `slug` (VARCHAR(150), Unique)
*   `description` (TEXT)
*   `address` (TEXT)
*   `map_coordinates` (VARCHAR(100)) - Google Maps Lat/Long
*   `operating_hours` (VARCHAR(100))
*   `ticket_price` (DECIMAL(10, 2))
*   `rating_avg` (DECIMAL(3, 2), Default: 0)
*   `is_featured` (BOOLEAN, Default: false) - Untuk highlight di beranda
*   `created_at` (TIMESTAMP)

#### 5. Table: `destination_galleries`
Menyimpan banyak foto untuk satu destinasi (One-to-Many).
*   `id` (INT, PK, Auto Increment)
*   `destination_id` (INT, FK -> destinations.id)
*   `image_url` (VARCHAR(255))
*   `caption` (VARCHAR(150))
*   `is_primary` (BOOLEAN) - Foto utama/thumbnail

#### 6. Table: `reviews`
Ulasan dan rating dari user untuk destinasi.
*   `id` (INT, PK, Auto Increment)
*   `user_id` (INT, FK -> users.id)
*   `destination_id` (INT, FK -> destinations.id)
*   `rating` (INT) - Skala 1-5
*   `comment` (TEXT)
*   `created_at` (TIMESTAMP)

#### 7. Table: `events`
Kalender acara pariwisata (Festival Erau, Pesta Laut, dll).
*   `id` (INT, PK, Auto Increment)
*   `destination_id` (INT, FK -> destinations.id, Nullable) - Bisa terikat destinasi atau umum
*   `title` (VARCHAR(150))
*   `description` (TEXT)
*   `start_date` (DATE)
*   `end_date` (DATE)
*   `poster_url` (VARCHAR(255))
*   `status` (ENUM('upcoming', 'ongoing', 'completed'))

#### 8. Table: `inquiries` (Contact Form)
Menyimpan pesan dari halaman "Contact Us".
*   `id` (INT, PK, Auto Increment)
*   `name` (VARCHAR(100))
*   `email` (VARCHAR(100))
*   `subject` (VARCHAR(150))
*   `message` (TEXT)
*   `is_read` (BOOLEAN, Default: false)
*   `created_at` (TIMESTAMP)

---

## 4. 🚀 Fitur Fungsional (Functional Requirements)

### 4.1. Halaman Publik (Pengunjung/Guest)
1.  **Homepage Interaktif**: Hero section dengan video/gambar parallax, Featured Destinations, dan Stats Counter.
2.  **Pencarian & Filter**: Mencari destinasi berdasarkan Nama, Kategori (Alam/Budaya), atau Lokasi (Kabupaten).
3.  **Detail Destinasi**: Halaman detail lengkap dengan galeri foto, peta lokasi, harga tiket, dan ulasan.
4.  **Event Calendar**: Melihat daftar event wisata yang akan datang.
5.  **Kirim Pesan**: Form kontak yang terhubung ke database `inquiries`.

### 4.2. Halaman User (Logged In)
1.  **Authentication**: Login dan Register akun.
2.  **Tulis Review**: Memberikan rating bintang dan komentar pada destinasi.
3.  **Wishlist (Opsional)**: Menyimpan destinasi favorit.

### 4.3. Halaman Admin (Dashboard)
1.  **Secure Login**: Akses khusus admin.
2.  **Dashboard Overview**: Statistik jumlah user, total destinasi, pesan belum dibaca.
3.  **Manajemen Destinasi (CRUD)**: Tambah, edit, hapus data wisata dan upload foto galeri.
4.  **Manajemen Event**: Update jadwal event wisata.
5.  **Moderasi Review**: Menghapus review yang tidak pantas.
6.  **Inbox**: Membaca pesan masuk dari form kontak.

---

## 5. 📅 Roadmap & Timeline Pengerjaan

### **Tahap 1: Perencanaan & Desain (Minggu 1)**
*   [x] Finalisasi Konsep & Requirement.
*   [ ] Perancangan UI/UX Detail (Mockup).
*   [ ] Setup Struktur Proyek Awal.

### **Tahap 2: Database & Backend Core (Minggu 2)**
*   [ ] Instalasi & Setup Database MySQL.
*   [ ] Membuat Tabel-tabel sesuai skema di atas.
*   [ ] Setup Node.js/Express Server.
*   [ ] Implementasi API Dasar (CRUD Destinations, Categories, Regencies).

### **Tahap 3: Integrasi Frontend-Backend (Minggu 3)**
*   [ ] Menghubungkan halaman Home dengan API (Fetch Data Featured).
*   [ ] Membuat halaman Detail Destinasi Dinamis (by ID/Slug).
*   [ ] Implementasi fitur Search & Filter.

### **Tahap 4: Fitur Interaktif & User System (Minggu 4)**
*   [ ] Implementasi Login/Register (JWT).
*   [ ] Fitur Review & Rating.
*   [ ] Form Contact Us -> Database.

### **Tahap 5: Admin Panel (Minggu 5)**
*   [ ] Membuat dashboard admin sederhana.
*   [ ] Form Input Data Destinasi (Upload Image).
*   [ ] Manajemen Pesan Masuk.

### **Tahap 6: Testing & Deployment (Minggu 6)**
*   [ ] Blackbox Testing (Fungsionalitas).
*   [ ] UX Tweaking (Animasi, Responsiveness).
*   [ ] Deployment ke Hosting.

---

## 6. 📝 API Endpoints Specification (Draft)

*   `GET /api/destinations` - Ambil semua destinasi (support query param: `?category=nature&regency=berau`).
*   `GET /api/destinations/:id` - Detail satu destinasi.
*   `POST /api/destinations` - (Admin) Tambah destinasi baru.
*   `GET /api/events` - Daftar event.
*   `POST /api/auth/login` - Login user/admin.
*   `POST /api/reviews` - Submit review (User only).
*   `POST /api/contact` - Kirim pesan kontak.

---
**Catatan:** Dokumen ini bersifat dinamis dan dapat diperbarui seiring berjalannya proyek.
