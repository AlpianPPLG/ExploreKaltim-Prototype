# 📋 Feature Planning & Development Roadmap
## Explore Kaltim - Tourism Booking Platform

**Dibuat:** 26 Januari 2026  
**Status:** Planning & Analysis  
**Tujuan:** Mengidentifikasi fitur yang belum berfungsi dan merencanakan pengembangan

---

## 🎯 Executive Summary

Dokumen ini berisi analisis lengkap tentang fitur-fitur yang sudah ada, yang masih belum berfungsi, dan yang perlu dikembangkan untuk sistem Explore Kaltim. Fokus utama adalah memastikan alur booking dari user sampai admin berfungsi dengan sempurna.

---

## ✅ Fitur Yang Sudah Berfungsi

### 1. Authentication & Authorization
- ✅ Login system (user & admin)
- ✅ Register system
- ✅ Session management
- ✅ Role-based access control (user/admin)
- ✅ Logout functionality

### 2. Public Pages
- ✅ Landing page (index.php)
- ✅ Explorasi destinasi (explorasi.php)
- ✅ Detail destinasi (detail.php)
- ✅ Navbar navigation
- ✅ Mobile responsive menu

### 3. User Features
- ✅ User dashboard
- ✅ View bookings list
- ✅ Booking form (checkout)
- ✅ Booking detail page
- ✅ Upload bukti pembayaran
- ✅ Form validation (client & server)

### 4. Admin Features
- ✅ Admin dashboard dengan statistik
- ✅ View all bookings
- ✅ Update booking status
- ✅ Manage destinations (CRUD)
- ✅ Manage users (view, update role, delete)

### 5. Database Structure
- ✅ Users table
- ✅ Destinations table
- ✅ Categories table
- ✅ Regencies table
- ✅ Packages table
- ✅ Bookings table
- ✅ Booking_details table
- ✅ Payments table
- ✅ Reviews table
- ✅ Destination_galleries table

---

## ❌ Fitur Yang Belum Berfungsi / Perlu Dikembangkan

### 🔴 PRIORITAS TINGGI

#### 1. Admin Booking Management - BELUM LENGKAP
**Status:** Partial - Hanya bisa update status, belum bisa lihat detail lengkap

**Yang Belum Ada:**
- ❌ Admin tidak bisa melihat detail lengkap booking (paket, tanggal travel, catatan user)
- ❌ Admin tidak bisa melihat bukti pembayaran yang diupload user
- ❌ Admin tidak bisa verifikasi pembayaran dengan mudah
- ❌ Tidak ada notifikasi untuk admin ketika ada booking baru
- ❌ Tidak ada notifikasi untuk admin ketika user upload bukti bayar

**Yang Perlu Ditambahkan:**
```
1. Halaman admin/booking_detail.php
   - Tampilkan semua info booking
   - Tampilkan bukti pembayaran
   - Tombol approve/reject payment
   - History status changes
   
2. Notifikasi System
   - Badge notifikasi di navbar admin
   - Alert untuk booking baru
   - Alert untuk payment upload baru
```

**Impact:** HIGH - Admin tidak bisa mengelola booking dengan efektif

---

#### 2. Payment Verification Flow - BELUM LENGKAP
**Status:** Partial - User bisa upload, tapi admin tidak bisa verifikasi dengan mudah

**Yang Belum Ada:**
- ❌ Admin tidak bisa melihat bukti pembayaran
- ❌ Tidak ada tombol approve/reject payment
- ❌ Tidak ada history perubahan status
- ❌ User tidak mendapat notifikasi setelah payment diverifikasi

**Yang Perlu Ditambahkan:**
```
1. Admin Payment Verification Page
   - List semua payment yang pending verification
   - Preview image bukti pembayaran
   - Tombol Approve → status jadi 'paid'
   - Tombol Reject → status kembali 'pending' + reason
   
2. User Notification
   - Email notification (optional)
   - In-app notification
   - Status update di booking detail
```

**Impact:** HIGH - Proses verifikasi pembayaran tidak bisa dilakukan

---

#### 3. Package Management - BELUM ADA
**Status:** Not Implemented - Paket wisata tidak bisa dikelola

**Yang Belum Ada:**
- ❌ Admin tidak bisa menambah paket wisata baru
- ❌ Admin tidak bisa edit paket yang sudah ada
- ❌ Admin tidak bisa delete paket
- ❌ Admin tidak bisa set paket active/inactive

**Yang Perlu Ditambahkan:**
```
1. admin/packages.php
   - List semua packages
   - Filter by destination
   - CRUD operations
   
2. admin/package_form.php
   - Form add/edit package
   - Fields: name, description, price, duration, destination_id
   - Toggle is_active
```

**Impact:** HIGH - Tidak bisa mengelola paket wisata

---

### 🟡 PRIORITAS SEDANG

#### 4. Review System - BELUM BERFUNGSI
**Status:** Database ready, UI not implemented

**Yang Belum Ada:**
- ❌ User tidak bisa submit review
- ❌ Review tidak muncul di detail destinasi (sudah ada query tapi data kosong)
- ❌ Admin tidak bisa moderate review

**Yang Perlu Ditambahkan:**
```
1. user/review.php (sudah ada tapi kosong)
   - Form submit review
   - Rating 1-5 stars
   - Comment textarea
   - Select destination (dari completed bookings)
   
2. Review Display
   - Tampilkan di detail.php (query sudah ada)
   - Sort by date
   - Show user avatar & name
   
3. admin/reviews.php
   - List all reviews
   - Approve/reject review
   - Delete inappropriate reviews
```

**Impact:** MEDIUM - User tidak bisa memberikan feedback

---

#### 5. Gallery Management - BELUM ADA
**Status:** Database ready, no admin interface

**Yang Belum Ada:**
- ❌ Admin tidak bisa upload gambar destinasi
- ❌ Admin tidak bisa set primary image
- ❌ Admin tidak bisa delete gambar

**Yang Perlu Ditambahkan:**
```
1. admin/destination_form.php enhancement
   - Upload multiple images
   - Set primary image
   - Delete image
   - Reorder images
   
2. Image Upload Handler
   - Validate file type (jpg, png, webp)
   - Resize & optimize
   - Save to server
   - Insert to destination_galleries table
```

**Impact:** MEDIUM - Destinasi tidak punya gambar yang bagus

---

#### 6. User Profile Management - BELUM LENGKAP
**Status:** Partial - User bisa lihat profile tapi tidak bisa edit

**Yang Belum Ada:**
- ❌ User tidak bisa edit profile (username, email)
- ❌ User tidak bisa ganti password
- ❌ User tidak bisa upload avatar custom

**Yang Perlu Ditambahkan:**
```
1. user/profile.php
   - Form edit profile
   - Change password
   - Upload avatar
   - Update email (with verification)
```

**Impact:** MEDIUM - User tidak bisa update informasi pribadi

---

### 🟢 PRIORITAS RENDAH (Nice to Have)

#### 7. Email Notifications - BELUM ADA
**Status:** Not Implemented

**Yang Perlu Ditambahkan:**
```
1. Email Templates
   - Booking confirmation
   - Payment verification success
   - Booking reminder (H-1)
   - Review request (after trip)
   
2. Email Service Integration
   - PHPMailer atau SMTP
   - Queue system (optional)
```

**Impact:** LOW - Sistem bisa jalan tanpa email, tapi UX lebih baik dengan email

---

#### 8. Search & Filter Enhancement - BASIC
**Status:** Basic search only

**Yang Bisa Ditingkatkan:**
```
1. Advanced Search
   - Price range filter
   - Date availability
   - Rating filter
   - Multiple categories
   
2. Sort Options
   - Sort by price
   - Sort by rating
   - Sort by popularity
```

**Impact:** LOW - Search dasar sudah cukup untuk MVP

---

#### 9. Dashboard Analytics - BASIC
**Status:** Basic stats only

**Yang Bisa Ditingkatkan:**
```
1. Admin Dashboard
   - Revenue chart (per bulan)
   - Booking trend graph
   - Popular destinations chart
   - User growth chart
   
2. Export Reports
   - Export booking data to Excel
   - Export revenue report
   - Export user list
```

**Impact:** LOW - Stats dasar sudah cukup

---

#### 10. Wishlist Feature - BELUM ADA
**Status:** Not Implemented

**Yang Perlu Ditambahkan:**
```
1. Database
   - wishlists table (user_id, destination_id)
   
2. User Interface
   - Heart icon di destination card
   - Wishlist page
   - Remove from wishlist
```

**Impact:** LOW - Nice to have tapi tidak critical

---

## 🚀 Roadmap Pengembangan

### Phase 1: Critical Features (Week 1-2)
**Tujuan:** Membuat alur booking dari user ke admin berfungsi sempurna

1. ✅ **Admin Booking Detail Page**
   - File: `admin/booking_detail.php`
   - Tampilkan semua info booking
   - Tampilkan bukti pembayaran
   - Tombol approve/reject

2. ✅ **Payment Verification System**
   - Update admin/bookings.php
   - Add payment verification flow
   - Add status history

3. ✅ **Package Management**
   - File: `admin/packages.php`
   - File: `admin/package_form.php`
   - CRUD operations

### Phase 2: User Experience (Week 3-4)
**Tujuan:** Meningkatkan pengalaman user

1. **Review System**
   - Implement user/review.php
   - Display reviews di detail.php
   - Admin moderation

2. **User Profile Management**
   - Edit profile
   - Change password
   - Upload avatar

3. **Gallery Management**
   - Upload multiple images
   - Set primary image
   - Delete images

### Phase 3: Enhancement (Week 5-6)
**Tujuan:** Fitur tambahan untuk meningkatkan kualitas

1. **Email Notifications**
   - Setup PHPMailer
   - Create email templates
   - Send notifications

2. **Advanced Search**
   - Price range filter
   - Date availability
   - Rating filter

3. **Dashboard Analytics**
   - Revenue charts
   - Booking trends
   - Export reports

---

## 📊 Priority Matrix

| Feature | Priority | Impact | Effort | Status |
|---------|----------|--------|--------|--------|
| Admin Booking Detail | 🔴 HIGH | HIGH | MEDIUM | Not Started |
| Payment Verification | 🔴 HIGH | HIGH | MEDIUM | Not Started |
| Package Management | 🔴 HIGH | HIGH | HIGH | Not Started |
| Review System | 🟡 MEDIUM | MEDIUM | MEDIUM | Not Started |
| Gallery Management | 🟡 MEDIUM | MEDIUM | HIGH | Not Started |
| User Profile Edit | 🟡 MEDIUM | MEDIUM | LOW | Not Started |
| Email Notifications | 🟢 LOW | LOW | HIGH | Not Started |
| Advanced Search | 🟢 LOW | LOW | MEDIUM | Not Started |
| Dashboard Analytics | 🟢 LOW | LOW | HIGH | Not Started |
| Wishlist | 🟢 LOW | LOW | LOW | Not Started |

---

## 🎯 Success Criteria

### Phase 1 Success Criteria:
- [ ] Admin bisa melihat detail lengkap setiap booking
- [ ] Admin bisa melihat bukti pembayaran yang diupload user
- [ ] Admin bisa approve/reject payment dengan mudah
- [ ] Admin bisa mengelola paket wisata (CRUD)
- [ ] User mendapat feedback setelah payment diverifikasi

### Phase 2 Success Criteria:
- [ ] User bisa submit review untuk destinasi yang sudah dikunjungi
- [ ] Review muncul di halaman detail destinasi
- [ ] User bisa edit profile dan ganti password
- [ ] Admin bisa upload multiple images untuk destinasi

### Phase 3 Success Criteria:
- [ ] User mendapat email notification untuk setiap status change
- [ ] User bisa filter destinasi dengan lebih advanced
- [ ] Admin bisa melihat analytics dan export reports

---

## 💡 Technical Notes

### Database Changes Needed:
```sql
-- Untuk notification system (optional)
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    type VARCHAR(50),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Untuk wishlist (optional)
CREATE TABLE wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    destination_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id),
    UNIQUE KEY unique_wishlist (user_id, destination_id)
);

-- Untuk payment history/audit trail
CREATE TABLE payment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by INT,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);
```

### File Structure untuk Phase 1:
```
admin/
├── booking_detail.php (NEW)
├── bookings.php (UPDATE)
├── packages.php (NEW)
├── package_form.php (NEW)
└── payment_verification.php (NEW - optional)

user/
├── booking_detail.php (UPDATE - add notification)
└── bookings.php (UPDATE - add status badges)

config/
└── email.php (NEW - for Phase 3)
```

---

## 🔧 Development Guidelines

### Coding Standards:
1. Gunakan prepared statements untuk semua query
2. Validasi input di server-side dan client-side
3. Escape output dengan `escapeOutput()` atau `htmlspecialchars()`
4. Gunakan session untuk menyimpan messages
5. Redirect setelah POST untuk prevent double submission

### Security Checklist:
- [ ] SQL Injection prevention (prepared statements)
- [ ] XSS prevention (escape output)
- [ ] CSRF protection (tokens)
- [ ] File upload validation
- [ ] Role-based access control
- [ ] Password hashing (bcrypt)

### Testing Checklist:
- [ ] Test semua form validation
- [ ] Test role-based access
- [ ] Test file upload
- [ ] Test payment flow
- [ ] Test email notifications
- [ ] Test mobile responsive

---

## 📞 Next Steps

1. **Review dokumen ini** dengan team/stakeholder
2. **Prioritaskan fitur** berdasarkan kebutuhan bisnis
3. **Mulai development Phase 1** - Admin Booking Management
4. **Setup development environment** yang proper
5. **Create task breakdown** untuk setiap fitur

---

## 📝 Notes & Questions

### Questions to Answer:
1. Apakah perlu payment gateway integration (Midtrans, Xendit)?
2. Apakah perlu multi-language support (ID/EN)?
3. Apakah perlu mobile app (React Native/Flutter)?
4. Apakah perlu real-time chat support?
5. Apakah perlu integration dengan Google Maps API?

### Technical Decisions:
1. Email service: PHPMailer vs SMTP vs SendGrid?
2. File storage: Local vs Cloud (AWS S3, Cloudinary)?
3. Image optimization: GD vs ImageMagick?
4. Analytics: Custom vs Google Analytics?

---

**Document Version:** 1.0  
**Last Updated:** 26 Januari 2026  
**Maintained By:** Development Team
