# 🔄 Database Migration Guide

## Cara Menjalankan Migrasi Database

### Langkah 1: Jalankan Migration Runner

Akses URL berikut di browser Anda:

```
http://localhost/ExploreKaltim/run_migrations.php
```

Script ini akan:
- ✅ Menambahkan kolom `rejection_reason` ke tabel `payments`
- ✅ Membuat tabel `payment_history` untuk tracking perubahan status
- ✅ Update enum `payment_status` untuk include 'approved' dan 'rejected'

### Langkah 2: Verifikasi Migrasi

Setelah migrasi berhasil, Anda akan melihat pesan:
```
✓ Migration 001_add_payment_features.sql completed
All migrations completed!
```

### Langkah 3: (Opsional) Jalankan Seed Data

Jika Anda ingin mengisi ulang data destinasi dan paket:

```
http://localhost/ExploreKaltim/migrate_seed.php
```

---

## File Migrasi yang Tersedia

### 001_add_payment_features.sql
**Tanggal:** 26 Januari 2026  
**Deskripsi:** Menambahkan fitur payment verification untuk admin

**Perubahan:**
1. Menambahkan kolom `rejection_reason` ke tabel `payments`
2. Membuat tabel `payment_history` untuk log perubahan status
3. Update enum `payment_status` untuk include status baru

---

## Troubleshooting

### Error: "Duplicate column name"
Ini normal jika kolom sudah ada. Script akan skip error ini.

### Error: "Table already exists"
Ini normal jika tabel sudah dibuat sebelumnya. Script akan skip error ini.

### Error: Connection failed
Pastikan:
- XAMPP/MySQL sudah running
- Database `explorekaltim` sudah dibuat
- Kredensial di `config/database.php` sudah benar

---

## Membuat Migrasi Baru

Jika Anda perlu membuat perubahan database baru:

1. Buat file baru di folder `migrations/` dengan format:
   ```
   002_nama_migrasi.sql
   ```

2. Tulis SQL statements di file tersebut

3. Jalankan `run_migrations.php` untuk apply perubahan

**Contoh:**
```sql
-- migrations/002_add_reviews_table.sql
USE explorekaltim;

CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

SELECT 'Migration 002 completed successfully!' as status;
```

---

## Best Practices

1. ✅ Selalu backup database sebelum migrasi
2. ✅ Test migrasi di development environment dulu
3. ✅ Gunakan `IF NOT EXISTS` untuk create table
4. ✅ Gunakan `ADD COLUMN IF NOT EXISTS` untuk alter table
5. ✅ Beri nama file migrasi dengan nomor urut (001, 002, dst)

---

**Last Updated:** 26 Januari 2026
