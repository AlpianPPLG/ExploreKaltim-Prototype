# 📚 Phase 1 - Quick Reference

## 🔗 URL Endpoints

### Admin Pages

| Page | URL | Description |
|------|-----|-------------|
| Bookings List | `/admin/bookings.php` | View all bookings with filters |
| Booking Detail | `/admin/booking_detail.php?id={id}` | View booking detail & payment verification |
| Packages List | `/admin/packages.php` | Manage packages (CRUD) |
| Add Package | `/admin/package_form.php` | Create new package |
| Edit Package | `/admin/package_form.php?id={id}` | Edit existing package |

### User Pages

| Page | URL | Description |
|------|-----|-------------|
| Booking Detail | `/user/booking_detail.php?id={id}` | View booking & payment status |

### Utility

| Page | URL | Description |
|------|-----|-------------|
| Run Migrations | `/run_migrations.php` | Apply database migrations |
| Seed Data | `/migrate_seed.php` | Insert sample data |

---

## 🗄️ Database Schema Changes

### New Table: payment_history

```sql
CREATE TABLE payment_history (
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

### Modified Table: payments

**New Column:**
```sql
ALTER TABLE payments 
ADD COLUMN rejection_reason TEXT AFTER payment_status;
```

**Updated Enum:**
```sql
ALTER TABLE payments 
MODIFY COLUMN payment_status ENUM('pending', 'approved', 'rejected', 'success', 'failed') DEFAULT 'pending';
```

---

## 📊 Status Flow

### Booking Status Flow

```
pending → waiting_payment → paid → confirmed → completed
                          ↓
                      cancelled
```

### Payment Status Flow

```
pending → approved → success
       ↓
    rejected → pending (user upload ulang)
```

---

## 🎨 Status Badge Colors

| Status | Class | Color |
|--------|-------|-------|
| pending | `bg-yellow-100 text-yellow-700` | Yellow |
| waiting_payment | `bg-orange-100 text-orange-700` | Orange |
| paid | `bg-blue-100 text-blue-700` | Blue |
| confirmed | `bg-indigo-100 text-indigo-700` | Indigo |
| completed | `bg-green-100 text-green-700` | Green |
| cancelled | `bg-red-100 text-red-700` | Red |
| approved | `bg-green-100 text-green-700` | Green |
| rejected | `bg-red-100 text-red-700` | Red |

---

## 🔐 Access Control

### Admin Only Pages
- `/admin/bookings.php`
- `/admin/booking_detail.php`
- `/admin/packages.php`
- `/admin/package_form.php`

**Check:**
```php
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');
```

### User Pages (Own Data Only)
- `/user/booking_detail.php`

**Check:**
```php
requireLogin('../login.php');
WHERE b.user_id = {$user['id']}
```

---

## 📝 Form Actions

### Approve Payment

**Form:**
```html
<form method="POST">
    <input type="hidden" name="payment_id" value="<?php echo $paymentId; ?>">
    <button type="submit" name="approve_payment">Approve</button>
</form>
```

**Handler:**
```php
if (isset($_POST['approve_payment'])) {
    // Update payment status to 'approved'
    // Update booking status to 'paid'
    // Log to payment_history
}
```

### Reject Payment

**Form:**
```html
<form method="POST">
    <input type="hidden" name="payment_id" value="<?php echo $paymentId; ?>">
    <textarea name="rejection_reason" required></textarea>
    <button type="submit" name="reject_payment">Reject</button>
</form>
```

**Handler:**
```php
if (isset($_POST['reject_payment'])) {
    // Update payment status to 'rejected'
    // Update booking status to 'pending'
    // Save rejection_reason
    // Log to payment_history
}
```

### Create/Update Package

**Form Fields:**
```php
destination_id (SELECT, required)
name (TEXT, required, max 255)
description (TEXTAREA, required)
price (NUMBER, required, min 0)
duration (TEXT, optional)
stock (NUMBER, optional, null = unlimited)
is_active (CHECKBOX, default true)
```

**Validation:**
```php
if (empty($name)) $errors[] = "Package name is required";
if ($destinationId <= 0) $errors[] = "Please select a destination";
if ($price < 0) $errors[] = "Price must be >= 0";
if (empty($description)) $errors[] = "Description is required";
```

---

## 🔍 Query Examples

### Get Booking with Full Details

```sql
SELECT b.*, u.username, u.email, u.avatar_url,
       bd.quantity, bd.price_per_unit, bd.travel_date, bd.note, bd.subtotal,
       p.name as package_name, p.description as package_desc,
       d.name as dest_name, d.slug as dest_slug, d.address as dest_address,
       pay.id as payment_id, pay.method, pay.payment_proof, pay.payment_status, 
       pay.rejection_reason, pay.created_at as payment_date, pay.paid_at,
       admin.username as verified_by_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN booking_details bd ON b.id = bd.booking_id
JOIN packages p ON bd.package_id = p.id
JOIN destinations d ON p.destination_id = d.id
LEFT JOIN payments pay ON b.id = pay.booking_id
LEFT JOIN users admin ON pay.verified_by = admin.id
WHERE b.id = ?
```

### Get Pending Payment Count

```sql
SELECT COUNT(*) as count 
FROM bookings 
WHERE status = 'waiting_payment'
```

### Get Payment History

```sql
SELECT ph.*, u.username as changed_by_name
FROM payment_history ph
JOIN users u ON ph.changed_by = u.id
WHERE ph.booking_id = ?
ORDER BY ph.created_at DESC
```

### Get Packages with Destination

```sql
SELECT p.*, d.name as dest_name, d.slug as dest_slug
FROM packages p
JOIN destinations d ON p.destination_id = d.id
WHERE p.destination_id = ? OR ? = 0
ORDER BY p.created_at DESC
```

---

## 🎯 Success Messages

### Session Messages

**Set:**
```php
$_SESSION['success_msg'] = "Payment approved successfully!";
$_SESSION['error_msg'] = "Failed to approve payment.";
```

**Display:**
```php
<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>
```

---

## 🧪 Test Credentials

### Admin Account
```
Username: admin
Password: admin123
Email: admin@explorekaltim.com
```

### User Account
```
Username: budi
Password: user123
Email: budi@gmail.com
```

---

## 📦 Dependencies

### Frontend
- Tailwind CSS (CDN)
- Font Awesome 6.4.0
- Google Fonts (Montserrat, Poppins)

### Backend
- PHP 7.4+
- MySQL 5.7+
- mysqli extension

---

## 🚀 Deployment Checklist

- [ ] Run migrations (`run_migrations.php`)
- [ ] Verify database schema
- [ ] Test all CRUD operations
- [ ] Test payment approval/rejection
- [ ] Test filters and search
- [ ] Test notification badge
- [ ] Test user notifications
- [ ] Verify access control
- [ ] Test on different browsers
- [ ] Test responsive design
- [ ] Backup database
- [ ] Update documentation

---

## 📞 Support

**Issues?** Check:
1. `docs/TESTING_PHASE_1.md` - Testing guide
2. `docs/MIGRATION_GUIDE.md` - Migration instructions
3. `docs/PHASE_1_COMPLETED.md` - Feature documentation

---

**Last Updated:** 26 Januari 2026  
**Version:** 1.0  
**Status:** Production Ready ✅
