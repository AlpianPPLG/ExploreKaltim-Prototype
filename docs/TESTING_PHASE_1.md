# 🧪 Testing Guide - Phase 1

## Quick Start Testing

### Prerequisites
1. ✅ XAMPP/MySQL running
2. ✅ Database `explorekaltim` sudah dibuat
3. ✅ Seed data sudah dijalankan

---

## Step 1: Run Database Migrations

Jalankan migrasi untuk menambahkan fitur payment verification:

```
http://localhost/ExploreKaltim/run_migrations.php
```

**Expected Output:**
```
✓ Migration 001_add_payment_features.sql completed
All migrations completed!
```

---

## Step 2: Test Admin Booking Management

### 2.1 Login sebagai Admin

```
URL: http://localhost/ExploreKaltim/login.php
Username: admin
Password: admin123
```

### 2.2 View Bookings List

```
URL: http://localhost/ExploreKaltim/admin/bookings.php
```

**Test Cases:**
- [ ] Lihat semua bookings
- [ ] Filter by status (pilih "Waiting Payment")
- [ ] Search by booking code
- [ ] Cek notification badge (harus ada angka jika ada pending payments)
- [ ] Click notification badge (harus redirect ke filtered list)

### 2.3 View Booking Detail

Click "View Detail" pada salah satu booking

**Test Cases:**
- [ ] Lihat informasi customer (nama, email, avatar)
- [ ] Lihat informasi destination & package
- [ ] Lihat booking details (tanggal, quantity, total)
- [ ] Lihat payment information
- [ ] Lihat payment proof (jika ada)

### 2.4 Approve Payment

Jika ada booking dengan status "waiting_payment":

**Test Cases:**
- [ ] Click "Approve Payment" button
- [ ] Cek success message muncul
- [ ] Cek status berubah ke "paid"
- [ ] Cek payment_status berubah ke "approved"
- [ ] Cek payment history tercatat

### 2.5 Reject Payment

**Test Cases:**
- [ ] Click "Reject Payment" button
- [ ] Modal muncul dengan form reason
- [ ] Input rejection reason
- [ ] Submit form
- [ ] Cek success message muncul
- [ ] Cek status berubah ke "pending"
- [ ] Cek payment_status berubah ke "rejected"
- [ ] Cek rejection_reason tersimpan
- [ ] Cek payment history tercatat

---

## Step 3: Test Package Management

### 3.1 View Packages List

```
URL: http://localhost/ExploreKaltim/admin/packages.php
```

**Test Cases:**
- [ ] Lihat semua packages
- [ ] Filter by destination
- [ ] Cek informasi package (name, price, duration, stock, status)

### 3.2 Add New Package

Click "Add Package" button

**Test Cases:**
- [ ] Select destination
- [ ] Input package name
- [ ] Input description
- [ ] Input price
- [ ] Input duration (optional)
- [ ] Input stock (optional, leave empty for unlimited)
- [ ] Toggle active/inactive
- [ ] Submit form
- [ ] Cek success message muncul
- [ ] Cek package muncul di list

### 3.3 Edit Package

Click "Edit" pada salah satu package

**Test Cases:**
- [ ] Form terisi dengan data existing
- [ ] Update informasi
- [ ] Submit form
- [ ] Cek success message muncul
- [ ] Cek perubahan tersimpan

### 3.4 Delete Package

Click "Delete" pada salah satu package

**Test Cases:**
- [ ] Confirmation modal muncul
- [ ] Confirm delete
- [ ] Cek success message muncul
- [ ] Cek package hilang dari list

### 3.5 Validation Testing

**Test Cases:**
- [ ] Submit form tanpa destination (harus error)
- [ ] Submit form tanpa package name (harus error)
- [ ] Submit form tanpa description (harus error)
- [ ] Submit form dengan price negatif (harus error)

---

## Step 4: Test User Notifications

### 4.1 Login sebagai User

```
URL: http://localhost/ExploreKaltim/login.php
Username: budi
Password: user123
```

### 4.2 View Booking Detail

```
URL: http://localhost/ExploreKaltim/user/booking_detail.php?id=1
```

### 4.3 Test Payment Approved Notification

Setelah admin approve payment:

**Test Cases:**
- [ ] Green banner muncul dengan pesan "Pembayaran Anda telah disetujui!"
- [ ] Status badge berubah
- [ ] Payment section berubah ke "Pembayaran Berhasil!"
- [ ] Tombol "Cetak Tiket / Invoice" muncul

### 4.4 Test Payment Rejected Notification

Setelah admin reject payment:

**Test Cases:**
- [ ] Red banner muncul dengan pesan "Pembayaran Anda ditolak"
- [ ] Rejection reason ditampilkan
- [ ] Form upload payment muncul kembali
- [ ] Heading berubah ke "Upload Ulang Bukti Bayar"
- [ ] User bisa upload bukti pembayaran baru

---

## Step 5: Test Filters & Search

### 5.1 Bookings Filter

```
URL: http://localhost/ExploreKaltim/admin/bookings.php
```

**Test Cases:**
- [ ] Filter by status "Pending" (hanya pending bookings muncul)
- [ ] Filter by status "Waiting Payment" (hanya waiting_payment muncul)
- [ ] Search by booking code (hasil sesuai)
- [ ] Search by username (hasil sesuai)
- [ ] Clear filter (semua bookings muncul kembali)

### 5.2 Packages Filter

```
URL: http://localhost/ExploreKaltim/admin/packages.php
```

**Test Cases:**
- [ ] Filter by destination (hanya packages dari destination tersebut muncul)
- [ ] Clear filter (semua packages muncul kembali)

---

## Step 6: Test Notification Badge

### 6.1 Create Test Scenario

1. Login sebagai user
2. Create booking baru
3. Upload payment proof
4. Logout

### 6.2 Check Admin Notification

1. Login sebagai admin
2. Go to bookings page
3. Check notification badge

**Test Cases:**
- [ ] Badge muncul dengan angka yang benar
- [ ] Click badge redirect ke filtered list (waiting_payment)
- [ ] Setelah approve/reject, badge count berkurang

---

## Step 7: Test Payment History

### 7.1 View Payment History

```
URL: http://localhost/ExploreKaltim/admin/booking_detail.php?id=1
```

**Test Cases:**
- [ ] Payment history section muncul
- [ ] Setiap perubahan status tercatat
- [ ] Informasi lengkap (old status, new status, changed by, reason, timestamp)
- [ ] Timeline ditampilkan dengan baik

---

## Step 8: Database Verification

### 8.1 Check Tables

Buka phpMyAdmin dan verify:

**payments table:**
- [ ] Kolom `rejection_reason` ada
- [ ] Enum `payment_status` include 'approved' dan 'rejected'

**payment_history table:**
- [ ] Table exists
- [ ] Columns: id, payment_id, booking_id, old_status, new_status, changed_by, reason, created_at
- [ ] Foreign keys setup correctly

---

## Expected Results Summary

| Feature | Status |
|---------|--------|
| Admin Booking Detail | ✅ |
| Payment Approval | ✅ |
| Payment Rejection | ✅ |
| Package CRUD | ✅ |
| Filters & Search | ✅ |
| Notification Badge | ✅ |
| User Notifications | ✅ |
| Payment History | ✅ |
| Validation | ✅ |
| Success Messages | ✅ |

---

## Common Issues & Solutions

### Issue 1: Migration Error
**Problem:** "Duplicate column name"  
**Solution:** Ini normal, kolom sudah ada. Skip error ini.

### Issue 2: Notification Badge Tidak Muncul
**Problem:** Badge count = 0  
**Solution:** Pastikan ada booking dengan status 'waiting_payment'

### Issue 3: Payment Proof Tidak Muncul
**Problem:** Image tidak load  
**Solution:** Pastikan URL valid dan accessible

### Issue 4: Success Message Tidak Muncul
**Problem:** Message tidak tampil setelah action  
**Solution:** Check session sudah started di config/session.php

---

## Performance Testing

### Load Testing
- [ ] Test dengan 100+ bookings
- [ ] Test dengan 50+ packages
- [ ] Check query performance
- [ ] Check page load time

### Stress Testing
- [ ] Multiple concurrent admin actions
- [ ] Multiple concurrent user uploads
- [ ] Database transaction handling

---

## Security Testing

### Authentication
- [ ] Non-admin tidak bisa akses admin pages
- [ ] User hanya bisa lihat booking mereka sendiri
- [ ] Session timeout berfungsi

### Input Validation
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] CSRF protection (if implemented)

### Authorization
- [ ] Admin bisa approve/reject semua payments
- [ ] User tidak bisa approve payment sendiri
- [ ] User tidak bisa edit booking orang lain

---

## Regression Testing

Setelah semua testing selesai, verify fitur lama masih berfungsi:

- [ ] User registration
- [ ] User login
- [ ] Create booking
- [ ] Upload payment proof
- [ ] View destinations
- [ ] View packages

---

## Sign Off

**Tested By:** _________________  
**Date:** _________________  
**Status:** [ ] PASSED [ ] FAILED  
**Notes:** _________________

---

**Last Updated:** 26 Januari 2026
