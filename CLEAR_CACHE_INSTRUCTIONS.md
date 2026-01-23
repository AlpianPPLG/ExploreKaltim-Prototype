# 🔄 Clear Cache Instructions

## Masalah yang Terjadi

Tombol "Booking" di navbar masih mengarah ke `#contact` padahal sudah diubah ke `login.php` di file `navbar.html`.

**Penyebab:** Browser meng-cache component HTML yang di-load oleh JavaScript.

---

## ✅ Solusi yang Sudah Diterapkan

### 1. Cache Busting di Component Loader
File `src/js/components.js` sudah diupdate dengan cache busting:

```javascript
// Sebelum:
const response = await fetch(path);

// Sesudah:
const cacheBuster = `?v=${Date.now()}`;
const response = await fetch(path + cacheBuster);
```

Ini akan menambahkan timestamp ke setiap request component, memaksa browser untuk load ulang file terbaru.

---

## 🔧 Cara Clear Cache di Browser

### Google Chrome / Microsoft Edge:

#### Metode 1: Hard Refresh (Paling Mudah)
1. Buka halaman `http://localhost/ExploreKaltim/`
2. Tekan **Ctrl + Shift + R** (Windows) atau **Cmd + Shift + R** (Mac)
3. Atau tekan **Ctrl + F5**

#### Metode 2: Clear Cache Manual
1. Tekan **Ctrl + Shift + Delete**
2. Pilih "Cached images and files"
3. Time range: "Last hour" atau "All time"
4. Klik "Clear data"
5. Refresh halaman dengan **F5**

#### Metode 3: DevTools
1. Buka DevTools dengan **F12**
2. Klik kanan pada tombol refresh di browser
3. Pilih "Empty Cache and Hard Reload"

---

### Mozilla Firefox:

#### Metode 1: Hard Refresh
1. Buka halaman `http://localhost/ExploreKaltim/`
2. Tekan **Ctrl + Shift + R** (Windows) atau **Cmd + Shift + R** (Mac)
3. Atau tekan **Ctrl + F5**

#### Metode 2: Clear Cache Manual
1. Tekan **Ctrl + Shift + Delete**
2. Pilih "Cache"
3. Time range: "Everything"
4. Klik "Clear Now"
5. Refresh halaman dengan **F5**

---

## 🧪 Testing Steps

### 1. Clear Cache
Gunakan salah satu metode di atas untuk clear cache browser.

### 2. Refresh Halaman
```
http://localhost/ExploreKaltim/
```

### 3. Test Tombol Booking
- **Hover** pada tombol "Booking" di navbar
- **Lihat URL** di status bar browser (pojok kiri bawah)
- **Seharusnya:** `http://localhost/ExploreKaltim/login.php`
- **Bukan:** `http://localhost/ExploreKaltim/#contact`

### 4. Klik Tombol
- Klik tombol "Booking"
- Seharusnya redirect ke halaman login
- URL: `http://localhost/ExploreKaltim/login.php`

### 5. Test Tombol Lainnya
- **Login button** → `login.php` ✅
- **Register button** → `register.php` ✅
- **Lihat Detail** (di card) → `login.php` ✅
- **Booking Sekarang** (CTA) → `login.php` ✅

---

## 🐛 Jika Masih Tidak Berhasil

### Opsi 1: Incognito/Private Mode
1. Buka browser dalam mode Incognito/Private
2. Akses `http://localhost/ExploreKaltim/`
3. Test tombol Booking

### Opsi 2: Disable Cache di DevTools
1. Buka DevTools (**F12**)
2. Buka tab "Network"
3. Centang "Disable cache"
4. Refresh halaman dengan **F5**
5. Biarkan DevTools tetap terbuka saat testing

### Opsi 3: Clear All Browser Data
1. **Chrome/Edge:** Settings → Privacy → Clear browsing data → All time
2. **Firefox:** Options → Privacy → Clear Data → Everything
3. Restart browser
4. Akses ulang `http://localhost/ExploreKaltim/`

### Opsi 4: Restart XAMPP
1. Stop Apache di XAMPP
2. Tunggu 5 detik
3. Start Apache lagi
4. Clear browser cache
5. Akses ulang halaman

---

## 📊 Verification Checklist

Setelah clear cache, pastikan semua ini benar:

- [ ] Hover tombol "Booking" → URL menunjukkan `login.php` (bukan `#contact`)
- [ ] Klik tombol "Booking" → Redirect ke halaman login
- [ ] Tombol "Login" → Redirect ke halaman login
- [ ] Tombol "Register" → Redirect ke halaman register
- [ ] Tombol "Lihat Detail" di card → Redirect ke login
- [ ] Tombol "Booking Sekarang" di CTA → Redirect ke login

---

## 💡 Tips untuk Development

### Untuk menghindari masalah cache di masa depan:

1. **Selalu gunakan Hard Refresh** saat development:
   - **Ctrl + Shift + R** atau **Ctrl + F5**

2. **Gunakan DevTools dengan "Disable cache"**:
   - Buka DevTools (**F12**)
   - Tab Network → Centang "Disable cache"
   - Biarkan DevTools terbuka saat development

3. **Gunakan Incognito Mode** untuk testing:
   - Tidak ada cache
   - Tidak ada cookies
   - Fresh state setiap kali

4. **Cache Busting sudah aktif**:
   - Component loader sekarang menambahkan timestamp
   - Setiap load akan fetch file terbaru
   - Tidak perlu manual clear cache lagi

---

## 🎯 Expected Result

Setelah clear cache, saat Anda hover tombol "Booking":

**Status Bar Browser (pojok kiri bawah) harus menunjukkan:**
```
http://localhost/ExploreKaltim/login.php
```

**BUKAN:**
```
http://localhost/ExploreKaltim/#contact
```

---

## 📝 Summary

1. ✅ File `navbar.html` sudah benar (href="login.php")
2. ✅ Cache busting sudah ditambahkan ke component loader
3. ⚠️ Browser masih meng-cache versi lama
4. 🔧 **Solusi:** Clear cache dengan **Ctrl + Shift + R**

---

**Setelah clear cache, semua tombol akan mengarah ke halaman yang benar!** 🎉

Jika masih ada masalah setelah clear cache, screenshot dan beritahu saya.
