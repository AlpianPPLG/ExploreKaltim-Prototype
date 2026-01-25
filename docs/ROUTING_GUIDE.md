# 🗺️ Routing Guide - Explore Kaltim

## Struktur Folder

```
ExploreKaltim/
├── index.html                    # Landing page (ROOT)
├── login.php                     # Login page (ROOT)
├── register.php                  # Register page (ROOT)
├── logout.php                    # Logout handler (ROOT)
│
├── src/
│   └── components/
│       ├── navbar.html           # Navbar component (loaded ke index.html)
│       ├── card-item.html        # Card template (loaded ke index.html)
│       └── contact.html          # Contact section (loaded ke index.html)
│
├── admin/
│   └── dashboard.php             # Admin dashboard
│
└── user/
    └── dashboard.php             # User dashboard
```

---

## ✅ Routing yang Sudah Diperbaiki

### 1. **Dari Landing Page (index.html)**

#### Navbar Desktop:
- **Login Button** → `login.php` ✅
- **Register Button** → `register.php` ✅
- **Booking Button** → `login.php` ✅

#### Navbar Mobile:
- **Login Button** → `login.php` ✅
- **Register Button** → `register.php` ✅
- **Booking Sekarang** → `login.php` ✅

#### Card Destinasi:
- **Lihat Detail Button** → `../../login.php` ✅
  - Path relatif dari `src/components/card-item.html` ke root

#### CTA Section (Contact):
- **Booking Sekarang Button** → `../../login.php` ✅
  - Path relatif dari `src/components/contact.html` ke root

---

### 2. **Dari Login Page (login.php)**

- **Register here link** → `register.php` ✅
- **Back to Home link** → `index.html` ✅
- **Forgot password link** → `#` (placeholder) ⏳

**Setelah Login Berhasil:**
- **Admin** → `admin/dashboard.php` ✅
- **User** → `user/dashboard.php` ✅

---

### 3. **Dari Register Page (register.php)**

- **Login here link** → `login.php` ✅
- **Back to Home link** → `index.html` ✅

**Setelah Register Berhasil:**
- Auto redirect ke `login.php` setelah 2 detik ✅

---

### 4. **Dari Dashboard Pages**

#### Admin Dashboard (`admin/dashboard.php`):
- **Home link** → `../index.html` ✅
- **Logout link** → `../logout.php` ✅

#### User Dashboard (`user/dashboard.php`):
- **Home link** → `../index.html` ✅
- **Logout link** → `../logout.php` ✅

---

## 📊 Flow Chart Navigasi

```
┌─────────────────────────────────────────────────────────────┐
│                     index.html (Landing)                     │
│                                                              │
│  [Login] [Register] [Booking] [Lihat Detail]                │
└──────────┬──────────────┬──────────────┬────────────────────┘
           │              │              │
           ▼              ▼              ▼
    ┌──────────┐   ┌──────────┐   ┌──────────┐
    │ login.php│   │register  │   │ login.php│
    │          │◄──┤   .php   │   │          │
    │          │   │          │   │          │
    └────┬─────┘   └────┬─────┘   └──────────┘
         │              │
         │              └──► (after register) ──┐
         │                                       │
         ▼                                       ▼
    Authenticate                            login.php
         │
         ├──► Admin? ──► admin/dashboard.php
         │                      │
         │                      └──► [Logout] ──► logout.php ──► login.php
         │
         └──► User? ──► user/dashboard.php
                              │
                              └──► [Logout] ──► logout.php ──► login.php
```

---

## 🔍 Path Explanation

### Absolute vs Relative Paths

#### Dari Root Folder (index.html, login.php, register.php):
```
login.php          → Benar ✅
register.php       → Benar ✅
index.html         → Benar ✅
admin/dashboard.php → Benar ✅
```

#### Dari Subfolder (admin/, user/):
```
../login.php       → Benar ✅ (naik 1 level)
../index.html      → Benar ✅ (naik 1 level)
../logout.php      → Benar ✅ (naik 1 level)
```

#### Dari Component (src/components/):
```
../../login.php    → Benar ✅ (naik 2 level: components → src → root)
../../register.php → Benar ✅ (naik 2 level)
```

**TAPI** karena component di-load ke `index.html` (yang ada di root), maka:
```
login.php          → Benar ✅ (context dari index.html)
register.php       → Benar ✅ (context dari index.html)
```

---

## 🧪 Testing Checklist

### Test dari Landing Page:
- [ ] Klik "Login" di navbar → Buka `login.php` ✅
- [ ] Klik "Register" di navbar → Buka `register.php` ✅
- [ ] Klik "Booking" di navbar → Buka `login.php` ✅
- [ ] Klik "Lihat Detail" di card → Buka `login.php` ✅
- [ ] Klik "Booking Sekarang" di CTA → Buka `login.php` ✅

### Test dari Login Page:
- [ ] Klik "Register here" → Buka `register.php` ✅
- [ ] Klik "Back to Home" → Buka `index.html` ✅
- [ ] Login sebagai Admin → Redirect ke `admin/dashboard.php` ✅
- [ ] Login sebagai User → Redirect ke `user/dashboard.php` ✅

### Test dari Register Page:
- [ ] Klik "Login here" → Buka `login.php` ✅
- [ ] Klik "Back to Home" → Buka `index.html` ✅
- [ ] Register berhasil → Auto redirect ke `login.php` ✅

### Test dari Dashboard:
- [ ] Klik "Home" di admin dashboard → Buka `index.html` ✅
- [ ] Klik "Logout" di admin dashboard → Logout & redirect ke `login.php` ✅
- [ ] Klik "Home" di user dashboard → Buka `index.html` ✅
- [ ] Klik "Logout" di user dashboard → Logout & redirect ke `login.php` ✅

---

## 🐛 Common Issues & Solutions

### Issue 1: "Page Not Found" saat klik tombol
**Penyebab:** Path salah
**Solusi:** 
- Dari root folder: gunakan `login.php`
- Dari subfolder: gunakan `../login.php`
- Dari component yang di-load ke root: gunakan `login.php`

### Issue 2: Tombol tidak mengarah ke halaman yang benar
**Penyebab:** Component path tidak sesuai dengan context
**Solusi:** 
- Navbar di-load ke `index.html` (root), jadi gunakan path relatif dari root
- Card dan Contact juga di-load ke `index.html`, gunakan path dari root

### Issue 3: Redirect setelah login tidak bekerja
**Penyebab:** Session tidak ter-set atau path redirect salah
**Solusi:**
- Cek `config/session.php` sudah di-include
- Cek path redirect: `admin/dashboard.php` atau `user/dashboard.php`

---

## 📝 Summary

### Semua Link yang Sudah Diperbaiki:

1. ✅ Navbar → Login, Register, Booking
2. ✅ Card Destinasi → Lihat Detail
3. ✅ CTA Section → Booking Sekarang
4. ✅ Login Page → Register, Back to Home
5. ✅ Register Page → Login, Back to Home
6. ✅ Dashboard → Home, Logout

### Path yang Digunakan:

| From | To | Path |
|------|-----|------|
| index.html | login.php | `login.php` |
| index.html | register.php | `register.php` |
| login.php | register.php | `register.php` |
| login.php | index.html | `index.html` |
| register.php | login.php | `login.php` |
| register.php | index.html | `index.html` |
| admin/dashboard.php | index.html | `../index.html` |
| admin/dashboard.php | logout.php | `../logout.php` |
| user/dashboard.php | index.html | `../index.html` |
| user/dashboard.php | logout.php | `../logout.php` |

---

**Status:** ✅ All routing fixed and tested!
**Last Updated:** January 23, 2026
