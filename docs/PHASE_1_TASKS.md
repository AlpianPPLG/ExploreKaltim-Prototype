# 📋 Phase 1: Critical Features - Task Breakdown
## Admin Booking Management System

**Timeline:** Week 1-2  
**Priority:** 🔴 HIGH  
**Goal:** Membuat alur booking dari user ke admin berfungsi sempurna

---

## Task 1: Admin Booking Detail Page

### 1.1 Create admin/booking_detail.php
**Estimasi:** 4 hours

**Requirements:**
- Tampilkan semua informasi booking lengkap
- Tampilkan informasi user (nama, email, avatar)
- Tampilkan informasi destinasi dan paket
- Tampilkan detail booking (tanggal travel, quantity, catatan)
- Tampilkan bukti pembayaran (jika sudah upload)
- Tampilkan history status changes

**Database Queries Needed:**
```sql
-- Get booking with all related data
SELECT b.*, u.username, u.email, u.avatar_url,
       bd.quantity, bd.price_per_unit, bd.travel_date, bd.note,
       p.name as package_name, p.description as package_desc,
       d.name as dest_name, d.slug as dest_slug,
       pay.method, pay.payment_proof, pay.payment_status, pay.created_at as payment_date
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN booking_details bd ON b.id = bd.booking_id
JOIN packages p ON bd.package_id = p.id
JOIN destinations d ON p.destination_id = d.id
LEFT JOIN payments pay ON b.id = pay.booking_id
WHERE b.id = ?
```

**UI Components:**
1. Header dengan booking code dan status badge
2. User information card
3. Destination & package information card
4. Booking details card (tanggal, quantity, total)
5. Payment information card
6. Payment proof preview (image/URL)
7. Action buttons (Approve/Reject payment)
8. Status history timeline

**Files to Create:**
- `admin/booking_detail.php`

**Files to Update:**
- `admin/bookings.php` - Add link to detail page

---

### 1.2 Payment Verification System
**Estimasi:** 3 hours

**Requirements:**
- Admin bisa approve payment → status jadi 'paid'
- Admin bisa reject payment → status kembali 'pending'
- Admin bisa input reason untuk rejection
- Update payment_status di table payments
- Update booking status di table bookings
- Log history perubahan status

**Form Actions:**
```php
// Approve Payment
if (isset($_POST['approve_payment'])) {
    // Update payments table
    UPDATE payments SET payment_status = 'approved' WHERE booking_id = ?
    
    // Update bookings table
    UPDATE bookings SET status = 'paid' WHERE id = ?
    
    // Insert to payment_history (optional)
    INSERT INTO payment_history (payment_id, old_status, new_status, changed_by, reason)
    
    // Redirect with success message
}

// Reject Payment
if (isset($_POST['reject_payment'])) {
    // Update payments table
    UPDATE payments SET payment_status = 'rejected' WHERE booking_id = ?
    
    // Update bookings table
    UPDATE bookings SET status = 'pending' WHERE id = ?
    
    // Insert to payment_history with reason
    
    // Redirect with success message
}
```

**UI Components:**
1. Approve button (green)
2. Reject button (red)
3. Reject reason modal/form
4. Confirmation dialog
5. Success/error messages

---

### 1.3 Enhanced Bookings List
**Estimasi:** 2 hours

**Requirements:**
- Add "View Detail" button untuk setiap booking
- Add filter by status
- Add search by booking code atau user name
- Add pagination (optional)
- Highlight bookings yang perlu action (waiting_payment)

**Updates to admin/bookings.php:**
```php
// Add filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Update query with filters
WHERE 1=1
AND ($statusFilter == '' OR b.status = '$statusFilter')
AND ($searchQuery == '' OR b.booking_code LIKE '%$searchQuery%' OR u.username LIKE '%$searchQuery%')
```

**UI Enhancements:**
1. Filter dropdown untuk status
2. Search box untuk booking code/username
3. "View Detail" button di setiap row
4. Badge untuk highlight pending actions
5. Better responsive table

---

## Task 2: Package Management System

### 2.1 Create admin/packages.php
**Estimasi:** 3 hours

**Requirements:**
- List semua packages dengan informasi destinasi
- Filter by destination
- Show active/inactive status
- CRUD operations (Create, Read, Update, Delete)
- Pagination

**Database Query:**
```sql
SELECT p.*, d.name as dest_name, d.slug as dest_slug
FROM packages p
JOIN destinations d ON p.destination_id = d.id
WHERE 1=1
AND ($destFilter == 0 OR p.destination_id = $destFilter)
ORDER BY p.created_at DESC
```

**UI Components:**
1. Header dengan "Add Package" button
2. Filter by destination dropdown
3. Table dengan columns:
   - Package Name
   - Destination
   - Price
   - Duration
   - Status (Active/Inactive)
   - Actions (Edit/Delete)
4. Delete confirmation dialog

**Files to Create:**
- `admin/packages.php`

---

### 2.2 Create admin/package_form.php
**Estimasi:** 4 hours

**Requirements:**
- Form untuk add/edit package
- Validation (client & server side)
- Select destination dari dropdown
- Toggle active/inactive
- Rich text editor untuk description (optional)

**Form Fields:**
```php
- destination_id (SELECT dropdown)
- name (TEXT, required, max 255)
- description (TEXTAREA, required)
- price (NUMBER, required, min 0)
- duration (TEXT, optional, e.g. "2 Hari 1 Malam")
- is_active (CHECKBOX, default true)
```

**Validation Rules:**
```php
$errors = [];

if (empty($name)) {
    $errors[] = "Package name is required";
}

if (empty($destination_id) || $destination_id == 0) {
    $errors[] = "Please select a destination";
}

if (empty($price) || $price < 0) {
    $errors[] = "Price must be greater than 0";
}

if (empty($description)) {
    $errors[] = "Description is required";
}
```

**Database Operations:**
```sql
-- Insert new package
INSERT INTO packages (destination_id, name, description, price, duration, is_active)
VALUES (?, ?, ?, ?, ?, ?)

-- Update existing package
UPDATE packages 
SET destination_id = ?, name = ?, description = ?, price = ?, duration = ?, is_active = ?
WHERE id = ?

-- Delete package
DELETE FROM packages WHERE id = ?
```

**Files to Create:**
- `admin/package_form.php`

---

### 2.3 Update Destination Form
**Estimasi:** 2 hours

**Requirements:**
- Add link to manage packages dari destination detail
- Show package count untuk setiap destination
- Quick add package button

**Updates to admin/destinations.php:**
```php
// Add package count to query
SELECT d.*, 
       (SELECT COUNT(*) FROM packages WHERE destination_id = d.id) as package_count
FROM destinations d
```

**UI Enhancements:**
1. Show package count badge
2. "Manage Packages" link
3. Quick stats

---

## Task 3: Notification System (Basic)

### 3.1 Add Notification Badge
**Estimasi:** 2 hours

**Requirements:**
- Show badge di navbar admin untuk pending actions
- Count bookings dengan status 'waiting_payment'
- Highlight badge dengan warna merah

**Query:**
```sql
SELECT COUNT(*) as pending_count 
FROM bookings 
WHERE status = 'waiting_payment'
```

**UI Component:**
```html
<a href="bookings.php?status=waiting_payment" class="relative">
    <i class="fas fa-bell"></i>
    <?php if ($pendingCount > 0): ?>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
            <?php echo $pendingCount; ?>
        </span>
    <?php endif; ?>
</a>
```

**Files to Update:**
- `admin/dashboard.php` - Add notification badge to navbar
- `admin/bookings.php` - Add notification badge to navbar

---

### 3.2 Add Success Messages
**Estimasi:** 1 hour

**Requirements:**
- Show success message setelah approve/reject payment
- Show success message setelah add/edit/delete package
- Use session untuk store messages
- Auto-dismiss after 5 seconds (optional)

**Implementation:**
```php
// Set message
$_SESSION['success_msg'] = "Payment approved successfully!";
$_SESSION['error_msg'] = "Failed to approve payment.";

// Display message
<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>
```

---

## Task 4: User Notification Enhancement

### 4.1 Update User Booking Detail
**Estimasi:** 2 hours

**Requirements:**
- Show notification jika payment approved
- Show notification jika payment rejected (dengan reason)
- Update status badge dengan warna yang sesuai
- Add "Upload Again" button jika rejected

**UI Enhancements:**
1. Success notification untuk approved payment
2. Error notification untuk rejected payment (dengan reason)
3. Status badge yang lebih jelas
4. Action buttons yang sesuai dengan status

**Files to Update:**
- `user/booking_detail.php`

---

## Testing Checklist

### Admin Booking Detail
- [x] Bisa melihat semua informasi booking ✓
- [x] Bisa melihat bukti pembayaran ✓
- [x] Bisa approve payment ✓
- [x] Bisa reject payment dengan reason ✓
- [x] Status berubah dengan benar ✓
- [x] Success message muncul ✓
- [x] Redirect ke halaman yang benar ✓

### Package Management
- [x] Bisa melihat list packages ✓
- [x] Bisa filter by destination ✓
- [x] Bisa add new package ✓
- [x] Bisa edit existing package ✓
- [x] Bisa delete package ✓
- [x] Validation berfungsi ✓
- [x] Success/error messages muncul ✓

### Notification System
- [x] Badge muncul di navbar admin ✓
- [x] Count pending actions benar ✓
- [x] Link ke filtered bookings berfungsi ✓
- [x] User mendapat notification setelah payment verified ✓

---

## Database Changes

### Optional: Create payment_history table
```sql
CREATE TABLE IF NOT EXISTS payment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    booking_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by INT NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);
```

### Update payments table (if needed)
```sql
ALTER TABLE payments 
ADD COLUMN rejection_reason TEXT AFTER payment_status;
```

---

## File Structure Summary

### New Files:
```
admin/
├── booking_detail.php (NEW)
├── packages.php (NEW)
└── package_form.php (NEW)
```

### Updated Files:
```
admin/
├── bookings.php (UPDATE)
├── dashboard.php (UPDATE)
└── destinations.php (UPDATE)

user/
└── booking_detail.php (UPDATE)
```

---

## Success Criteria

Phase 1 dianggap selesai jika:
- [x] Admin bisa melihat detail lengkap setiap booking ✓
- [x] Admin bisa melihat bukti pembayaran yang diupload user ✓
- [x] Admin bisa approve/reject payment dengan mudah ✓
- [x] Admin bisa mengelola paket wisata (CRUD) ✓
- [x] User mendapat feedback setelah payment diverifikasi ✓
- [x] Notification badge berfungsi di navbar admin ✓
- [x] Semua validasi berfungsi dengan baik ✓
- [x] Success/error messages muncul dengan benar ✓

---

## Timeline Breakdown

| Task | Estimasi | Priority |
|------|----------|----------|
| Admin Booking Detail Page | 4h | HIGH |
| Payment Verification System | 3h | HIGH |
| Enhanced Bookings List | 2h | MEDIUM |
| Create packages.php | 3h | HIGH |
| Create package_form.php | 4h | HIGH |
| Update Destination Form | 2h | LOW |
| Add Notification Badge | 2h | MEDIUM |
| Add Success Messages | 1h | LOW |
| Update User Booking Detail | 2h | MEDIUM |

**Total Estimasi:** 23 hours (~3 hari kerja)

---

**Next Phase:** Phase 2 - User Experience Enhancement  
**Document Version:** 1.0  
**Last Updated:** 26 Januari 2026
