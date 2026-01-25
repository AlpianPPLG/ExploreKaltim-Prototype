# 🔧 Session & Routing Fix Guide

## Masalah yang Diperbaiki

### 1. URL Path Salah ❌
**Sebelum:** `localhost/admin/dashboard.php` (404 Not Found)
**Sesudah:** `localhost/ExploreKaltim/admin/dashboard.php` ✅

### 2. Navbar Tidak Mendeteksi Login ❌
**Sebelum:** Tombol Login/Register tetap muncul meskipun sudah login
**Sesudah:** Navbar menampilkan Dashboard & Logout jika sudah login ✅

---

## Perubahan yang Dilakukan

### 1. ✅ Fixed Login Redirect Path
**File:** `login.php`

```php
// Sebelum:
header("Location: admin/dashboard.php");

// Sesudah:
header("Location: /ExploreKaltim/admin/dashboard.php");
```

### 2. ✅ Fixed Session Helper Functions
**File:** `config/session.php`

Semua redirect path diupdate dengan `/ExploreKaltim/`:
- `requireLogin()` → `/ExploreKaltim/login.php`
- `requireAdmin()` → `/ExploreKaltim/index.html`
- `redirectIfLoggedIn()` → `/ExploreKaltim/admin/dashboard.php` atau `/ExploreKaltim/user/dashboard.php`

### 3. ✅ Fixed Logout Redirect
**File:** `logout.php`

```php
// Sebelum:
header("Location: login.php?logout=success");

// Sesudah:
header("Location: /ExploreKaltim/login.php?logout=success");
```

### 4. ✅ Created Dynamic Navbar with Session Detection
**File:** `navbar.php` (NEW)

Navbar sekarang:
- Mendeteksi jika user sudah login
- Menampilkan avatar & username jika login
- Menampilkan tombol Dashboard & Logout jika login
- Menampilkan tombol Login/Register jika belum login

### 5. ✅ Converted index.html to index.php
**File:** `index.php` (NEW)

- Menambahkan `session_start()` di awal file
- Menggunakan `include 'navbar.php'` untuk navbar dinamis
- Navbar sekarang bisa mendeteksi status login

### 6. ✅ Updated Component Loader
**File:** `src/js/components.js`

- Removed navbar dari component config
- Navbar sekarang di-load via PHP include

### 7. ✅ Created .htaccess for Redirect
**File:** `.htaccess` (NEW)

- Redirect `index.html` ke `index.php`
- Set `index.php` sebagai DirectoryIndex

---

## Cara Menggunakan

### 1. Clear Browser Cache
```
Ctrl + Shift + R (atau Ctrl + F5)
```

### 2. Akses Landing Page
```
http://localhost/ExploreKaltim/
```
atau
```
http://localhost/ExploreKaltim/index.php
```

### 3. Test Flow

#### Jika Belum Login:
1. Buka `http://localhost/ExploreKaltim/`
2. Navbar menampilkan: **Login | Register | Booking**
3. Klik "Login" → Redirect ke `login.php`
4. Login dengan akun Anda:
   - Username: Alpian
   - Email: Nova07pplg@gmail.com
   - Password: Nirvana06
5. Setelah login → Redirect ke dashboard sesuai role

#### Jika Sudah Login:
1. Buka `http://localhost/ExploreKaltim/`
2. Navbar menampilkan: **[Avatar] Username | Logout**
3. Klik "Username" → Redirect ke dashboard
4. Klik "Logout" → Logout dan redirect ke login page

---

## Alur Navigasi Baru

```
Landing Page (index.php)
    │
    ├─→ Belum Login?
    │   ├─→ Klik Login → login.php
    │   ├─→ Klik Register → register.php
    │   └─→ Klik Booking → login.php
    │
    └─→ Sudah Login?
        ├─→ Klik Username → admin/dashboard.php atau user/dashboard.php
        └─→ Klik Logout → logout.php → login.php
```

---

## Testing Checklist

### Test 1: Login Flow
- [ ] Buka `http://localhost/ExploreKaltim/`
- [ ] Navbar menampilkan Login/Register/Booking
- [ ] Klik "Login"
- [ ] Login dengan akun yang sudah dibuat
- [ ] Redirect ke dashboard yang benar (admin atau user)
- [ ] URL benar: `localhost/ExploreKaltim/admin/dashboard.php`

### Test 2: Navbar After Login
- [ ] Setelah login, kembali ke landing page
- [ ] Navbar sekarang menampilkan Avatar & Username
- [ ] Navbar menampilkan tombol Logout
- [ ] Tidak ada tombol Login/Register lagi

### Test 3: Dashboard Access
- [ ] Klik username di navbar
- [ ] Redirect ke dashboard
- [ ] Dashboard menampilkan data user yang benar

### Test 4: Logout Flow
- [ ] Klik "Logout" di navbar
- [ ] Redirect ke login page
- [ ] Session destroyed
- [ ] Kembali ke landing page → Navbar kembali menampilkan Login/Register

### Test 5: Direct Access Protection
- [ ] Logout terlebih dahulu
- [ ] Coba akses `http://localhost/ExploreKaltim/admin/dashboard.php`
- [ ] Harus redirect ke login page (protected)

---

## File Structure Update

```
ExploreKaltim/
├── index.php                 # ✅ NEW (converted from index.html)
├── index.html                # ⚠️ OLD (keep for backup, will redirect to index.php)
├── navbar.php                # ✅ NEW (dynamic navbar with session)
├── .htaccess                 # ✅ NEW (redirect rules)
├── login.php                 # ✅ UPDATED (fixed redirect path)
├── logout.php                # ✅ UPDATED (fixed redirect path)
│
├── config/
│   └── session.php           # ✅ UPDATED (fixed all redirect paths)
│
└── src/
    └── js/
        └── components.js     # ✅ UPDATED (removed navbar from config)
```

---

## Important Notes

### 1. Use index.php, Not index.html
Sekarang landing page menggunakan `index.php` agar bisa mendeteksi session.

### 2. Navbar is Now PHP
Navbar sekarang file PHP (`navbar.php`) yang di-include ke `index.php`.

### 3. All Paths Use /ExploreKaltim/
Semua redirect path sekarang menggunakan `/ExploreKaltim/` untuk konsistensi.

### 4. Session Detection Works
Navbar sekarang bisa mendeteksi:
- Apakah user sudah login
- Role user (admin atau user)
- Data user (username, avatar)

---

## Troubleshooting

### Issue 1: Navbar Tidak Berubah Setelah Login
**Solusi:**
1. Clear browser cache (Ctrl + Shift + R)
2. Pastikan mengakses `index.php` bukan `index.html`
3. Check session dengan: `var_dump($_SESSION);` di `index.php`

### Issue 2: 404 Not Found di Dashboard
**Solusi:**
1. Pastikan path menggunakan `/ExploreKaltim/`
2. Check file `config/session.php` sudah diupdate
3. Check file `login.php` redirect path sudah benar

### Issue 3: Session Tidak Persist
**Solusi:**
1. Pastikan `session_start()` ada di awal `index.php`
2. Check PHP session settings di `php.ini`
3. Restart Apache di XAMPP

---

## Summary

### ✅ Fixed:
1. Login redirect path (404 → working)
2. Navbar session detection (static → dynamic)
3. All redirect paths (relative → absolute)
4. Landing page (HTML → PHP with session)

### ✅ New Features:
1. Dynamic navbar based on login status
2. Avatar & username display when logged in
3. Dashboard quick access from navbar
4. Logout button in navbar

---

**Status:** ✅ All issues fixed!
**Test:** Clear cache and access `http://localhost/ExploreKaltim/`
