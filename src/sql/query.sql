-- Database Creation
CREATE DATABASE IF NOT EXISTS explorekaltim;
USE explorekaltim;

-- ==========================================
-- 1. Table: Users
-- Menyimpan data admin dan user terdaftar
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. Table: Regencies (Kabupaten/Kota)
-- Data administratif daerah di Kalimantan Timur
-- ==========================================
CREATE TABLE IF NOT EXISTS regencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Contoh: "Samarinda", "Berau"
    code VARCHAR(10) DEFAULT NULL, -- Kode unik area jika ada
    description TEXT,
    thumbnail_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 3. Table: Categories
-- Pengelompokan jenis wisata (Alam, Budaya, Kuliner)
-- ==========================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon_class VARCHAR(50) DEFAULT NULL, -- Class icon font awesome / lainnya
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 4. Table: Destinations
-- Data utama tempat wisata
-- ==========================================
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    regency_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    address TEXT,
    map_coordinates VARCHAR(100) DEFAULT NULL, -- Latitude, Longitude
    operating_hours VARCHAR(100) DEFAULT NULL,
    ticket_price DECIMAL(10, 2) DEFAULT 0,
    rating_avg DECIMAL(3, 2) DEFAULT 0.00,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (regency_id) REFERENCES regencies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ==========================================
-- 5. Table: Destination Galleries
-- Foto-foto detail untuk setiap destinasi
-- ==========================================
CREATE TABLE IF NOT EXISTS destination_galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(150) DEFAULT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- ==========================================
-- 6. Table: Reviews
-- Ulasan dan rating komunal
-- ==========================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- ==========================================
-- 7. Table: Events
-- Kalender event pariwisata
-- ==========================================
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT DEFAULT NULL, -- Bisa null jika event umum/kota
    title VARCHAR(150) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    poster_url VARCHAR(255) DEFAULT NULL,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
);

-- ==========================================
-- 8. Table: Inquiries
-- Pesan dari form Contact Us
-- ==========================================
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 9. Table: Packages (Tiket/Paket Wisata)
-- Produk yang bisa dibeli untuk setiap destinasi
-- Contoh: "Tiket Masuk Dewasa", "Paket Camping 2D1N"
-- ==========================================
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    name VARCHAR(150) NOT NULL, -- "Tiket Masuk Weekday", "Speedboat Tour"
    description TEXT,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0,
    stock INT DEFAULT NULL, -- NULL jika unlimited (seperti tiket masuk umum)
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- ==========================================
-- 10. Table: Bookings (Transaksi Reservasi)
-- Header transaksi pembelian
-- ==========================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_code VARCHAR(20) NOT NULL UNIQUE, -- Contoh: "INV-20240122-001"
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'waiting_payment', 'paid', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Tanggal transaksi dibuat
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 11. Table: Booking Details (Item Transaksi)
-- Menyimpan detail tiket/paket apa saja yang dibeli dalam 1 transaksi
-- ==========================================
CREATE TABLE IF NOT EXISTS booking_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    package_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_per_unit DECIMAL(10, 2) NOT NULL, -- Harga saat transaksi terjadi (untuk history akurat)
    subtotal DECIMAL(10, 2) NOT NULL,
    travel_date DATE NOT NULL, -- Tanggal kunjungan user
    note TEXT, -- Catatan khusus (misal: "Alergi kacang" untuk paket makan)
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT
);

-- ==========================================
-- 12. Table: Payments (Pembayaran)
-- Mencatat riwayat pembayaran
-- ==========================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    method VARCHAR(50), -- "Bank Transfer", "E-Wallet", "Credit Card"
    payment_proof VARCHAR(255) DEFAULT NULL, -- URL gambar bukti transfer (manual)
    transaction_id VARCHAR(100) DEFAULT NULL, -- External ID dari Payment Gateway (Midtrans/Xendit)
    payment_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- ==========================================
-- Seed Data (Data Awal Dummy)
-- ==========================================

-- Insert Regencies
INSERT INTO regencies (name, description) VALUES 
('Samarinda', 'Ibu kota provinsi dengan pesona Sungai Mahakam'),
('Balikpapan', 'Kota minyak yang bersih dan modern dengan pantai indah'),
('Berau', 'Gerbang menuju surga bawah laut Derawan'),
('Kutai Kartanegara', 'Pusat sejarah kerajaan tertua di Indonesia');

-- Insert Categories
INSERT INTO categories (name, slug, icon_class) VALUES 
('Wisata Alam', 'nature', 'fas fa-tree'),
('Wisata Bahari', 'beach', 'fas fa-water'),
('Wisata Budaya', 'culture', 'fas fa-landmark'),
('Wisata Kuliner', 'culinary', 'fas fa-utensils');

-- Insert Admin User (Password: admin123 -> Hash needs to be generated in app, using placeholder here)
INSERT INTO users (username, email, password_hash, role) VALUES 
('admin', 'admin@explorekaltim.com', '$2b$10$PlaceHolderHashForAdmin123', 'admin'),
('budi', 'budi@gmail.com', '$2b$10$PlaceHolderHash', 'user');
