# 🚀 Quick Start Development Guide
## Explore Kaltim - Phase 1 Implementation

**Target:** Implement 3 critical features dalam 2-3 hari  
**Priority:** 🔴 HIGH  
**Status:** Ready to Start

---

## 📋 Pre-Development Checklist

### Environment Setup
- [ ] XAMPP/WAMP installed dan running
- [ ] PHP 7.4+ installed
- [ ] MySQL/MariaDB running
- [ ] Database `explore_kaltim` sudah ada
- [ ] All tables sudah di-migrate
- [ ] Sample data sudah di-seed
- [ ] Code editor ready (VS Code recommended)

### Access Verification
- [ ] Bisa akses http://localhost/ExploreKaltim/
- [ ] Bisa login sebagai admin
- [ ] Bisa login sebagai user
- [ ] Bisa create booking sebagai user
- [ ] Bisa upload bukti pembayaran

### Git Setup (Optional but Recommended)
```bash
cd C:\xampp\htdocs\ExploreKaltim
git init
git add .
git commit -m "Initial commit before Phase 1"
git branch phase-1-admin-booking
git checkout phase-1-admin-booking
```

---

## 🎯 Phase 1: Implementation Order

### Day 1: Admin Booking Detail Page (4-6 hours)

#### Step 1.1: Create admin/booking_detail.php (2 hours)
```php
<?php
/**
 * File: admin/booking_detail.php
 * Purpose: Display full booking details with payment proof
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get booking with all related data
$query = "
    SELECT b.*, u.username, u.email, u.avatar_url,
           bd.quantity, bd.price_per_unit, bd.travel_date, bd.note,
           p.name as package_name, p.description as package_desc,
           d.name as dest_name, d.slug as dest_slug,
           r.name as regency_name,
           pay.method, pay.payment_proof, pay.payment_status, 
           pay.created_at as payment_date
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN booking_details bd ON b.id = bd.booking_id
    JOIN packages p ON bd.package_id = p.id
    JOIN destinations d ON p.destination_id = d.id
    JOIN regencies r ON d.regency_id = r.id
    LEFT JOIN payments pay ON b.id = pay.booking_id
    WHERE b.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: bookings.php");
    exit();
}

$booking = $result->fetch_assoc();
closeDBConnection($conn);
?>

<!-- HTML Template -->
<!-- Copy structure dari user/booking_detail.php -->
<!-- Tambahkan section untuk payment proof -->
<!-- Tambahkan action buttons (Approve/Reject) -->
```

**Checklist:**
- [ ] File created
- [ ] Query working
- [ ] Data displayed correctly
- [ ] Payment proof visible
- [ ] Responsive design
- [ ] Back button working

---

#### Step 1.2: Add Payment Verification (2 hours)
```php
// Add to admin/booking_detail.php

// Handle Approve Payment
if (isset($_POST['approve_payment'])) {
    $conn = getDBConnection();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update payment status
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'approved' WHERE booking_id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Update booking status
        $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_msg'] = "Payment approved successfully!";
        header("Location: booking_detail.php?id=" . $bookingId);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to approve payment: " . $e->getMessage();
    }
    
    closeDBConnection($conn);
}

// Handle Reject Payment
if (isset($_POST['reject_payment'])) {
    $reason = trim($_POST['rejection_reason']);
    $conn = getDBConnection();
    
    $conn->begin_transaction();
    
    try {
        // Update payment status
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'rejected' WHERE booking_id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Update booking status back to pending
        $stmt = $conn->prepare("UPDATE bookings SET status = 'pending' WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Optional: Save rejection reason
        // You might want to add a rejection_reason column to payments table
        
        $conn->commit();
        
        $_SESSION['success_msg'] = "Payment rejected. User can upload again.";
        header("Location: booking_detail.php?id=" . $bookingId);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to reject payment: " . $e->getMessage();
    }
    
    closeDBConnection($conn);
}
```

**Checklist:**
- [ ] Approve button working
- [ ] Reject button working
- [ ] Status updated correctly
- [ ] Transaction rollback on error
- [ ] Success message displayed
- [ ] Redirect working

---

#### Step 1.3: Update admin/bookings.php (1 hour)
```php
// Add "View Detail" button to each row

<td class="px-6 py-4 text-right">
    <div class="flex items-center justify-end gap-3">
        <!-- Add this button -->
        <a href="booking_detail.php?id=<?php echo $booking['id']; ?>" 
           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
            View Detail
        </a>
        
        <!-- Existing status update form -->
        <form method="POST" class="flex items-center gap-2">
            <!-- ... existing code ... -->
        </form>
    </div>
</td>
```

**Checklist:**
- [ ] View Detail button added
- [ ] Link working correctly
- [ ] Button styled properly

---

### Day 2: Package Management (6-8 hours)

#### Step 2.1: Create admin/packages.php (3 hours)
```php
<?php
/**
 * File: admin/packages.php
 * Purpose: List and manage all packages
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Package deleted successfully.";
    }
    header("Location: packages.php");
    exit();
}

// Get all packages with destination info
$query = "
    SELECT p.*, d.name as dest_name, d.slug as dest_slug
    FROM packages p
    JOIN destinations d ON p.destination_id = d.id
    ORDER BY p.created_at DESC
";
$result = $conn->query($query);
$packages = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>

<!-- HTML Template -->
<!-- Similar structure to admin/destinations.php -->
<!-- Table with columns: Package Name, Destination, Price, Duration, Status, Actions -->
```

**Checklist:**
- [ ] File created
- [ ] List packages working
- [ ] Delete working
- [ ] Add Package button visible
- [ ] Edit link working
- [ ] Responsive table

---

#### Step 2.2: Create admin/package_form.php (3 hours)
```php
<?php
/**
 * File: admin/package_form.php
 * Purpose: Add/Edit package form
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Check if editing
$isEdit = isset($_GET['id']) && !empty($_GET['id']);
$packageId = $isEdit ? (int)$_GET['id'] : 0;

if ($isEdit) {
    // Get package data
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: packages.php");
        exit();
    }
    
    $package = $result->fetch_assoc();
}

// Get all destinations for dropdown
$destinations = $conn->query("SELECT id, name FROM destinations ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination_id = (int)$_POST['destination_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $duration = trim($_POST['duration']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Package name is required";
    }
    
    if ($destination_id == 0) {
        $errors[] = "Please select a destination";
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (empty($errors)) {
        if ($isEdit) {
            // Update
            $stmt = $conn->prepare("UPDATE packages SET destination_id = ?, name = ?, description = ?, price = ?, duration = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("issdsii", $destination_id, $name, $description, $price, $duration, $is_active, $packageId);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO packages (destination_id, name, description, price, duration, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdsi", $destination_id, $name, $description, $price, $duration, $is_active);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = $isEdit ? "Package updated successfully!" : "Package created successfully!";
            header("Location: packages.php");
            exit();
        } else {
            $errors[] = "Failed to save package. Please try again.";
        }
    }
}

closeDBConnection($conn);
?>

<!-- HTML Form -->
<!-- Fields: destination_id (select), name, description, price, duration, is_active (checkbox) -->
```

**Checklist:**
- [ ] File created
- [ ] Add form working
- [ ] Edit form working
- [ ] Validation working
- [ ] Success message displayed
- [ ] Redirect working

---

### Day 3: Notification & Polish (2-3 hours)

#### Step 3.1: Add Notification Badge (1 hour)
```php
// Add to admin/dashboard.php and admin/bookings.php navbar

<?php
// Get pending payment count
$conn = getDBConnection();
$result = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'waiting_payment'");
$pendingCount = $result->fetch_assoc()['count'];
closeDBConnection($conn);
?>

<!-- In navbar -->
<a href="bookings.php?status=waiting_payment" class="relative">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
    </svg>
    <?php if ($pendingCount > 0): ?>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
            <?php echo $pendingCount; ?>
        </span>
    <?php endif; ?>
</a>
```

**Checklist:**
- [ ] Badge visible
- [ ] Count correct
- [ ] Link working
- [ ] Styled properly

---

#### Step 3.2: Add Filter & Search (1 hour)
```php
// Add to admin/bookings.php

// Get filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Update query
$query = "
    SELECT b.*, u.username, u.email,
           (SELECT p.name FROM booking_details bd JOIN packages p ON bd.package_id = p.id WHERE bd.booking_id = b.id LIMIT 1) as package_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE 1=1
";

if (!empty($statusFilter)) {
    $query .= " AND b.status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if (!empty($searchQuery)) {
    $search = $conn->real_escape_string($searchQuery);
    $query .= " AND (b.booking_code LIKE '%$search%' OR u.username LIKE '%$search%')";
}

$query .= " ORDER BY b.booking_date DESC";
```

**HTML Filter Form:**
```html
<div class="mb-6 flex gap-4">
    <form method="GET" class="flex gap-3">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="waiting_payment">Waiting Payment</option>
            <option value="paid">Paid</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        
        <input type="text" name="search" placeholder="Search booking code or username..." 
               class="border rounded px-3 py-2 w-64">
        
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded">
            Filter
        </button>
    </form>
</div>
```

**Checklist:**
- [ ] Filter by status working
- [ ] Search working
- [ ] Results correct
- [ ] UI clean

---

## 🧪 Testing Checklist

### Admin Booking Detail
- [ ] Bisa akses admin/booking_detail.php?id=1
- [ ] Semua info booking tampil
- [ ] Bukti pembayaran tampil (jika ada)
- [ ] Approve button berfungsi
- [ ] Reject button berfungsi
- [ ] Status berubah dengan benar
- [ ] Success message muncul
- [ ] Redirect ke halaman yang benar

### Package Management
- [ ] Bisa akses admin/packages.php
- [ ] List packages tampil
- [ ] Add package berfungsi
- [ ] Edit package berfungsi
- [ ] Delete package berfungsi
- [ ] Validation berfungsi
- [ ] Success/error messages muncul

### Notification & Filter
- [ ] Badge notifikasi muncul
- [ ] Count benar
- [ ] Link ke filtered bookings berfungsi
- [ ] Filter by status berfungsi
- [ ] Search berfungsi

### Integration Testing
- [ ] User bisa booking
- [ ] User bisa upload bukti bayar
- [ ] Admin bisa lihat booking detail
- [ ] Admin bisa approve payment
- [ ] User lihat status update
- [ ] Admin bisa manage packages
- [ ] Packages muncul di detail.php

---

## 🐛 Common Issues & Solutions

### Issue 1: "Call to undefined function requireAdmin()"
**Solution:** Make sure `config/security.php` has the function:
```php
function requireAdmin($redirectUrl = 'index.php') {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: $redirectUrl");
        exit();
    }
}
```

### Issue 2: Payment proof not showing
**Solution:** Check if payment record exists:
```sql
SELECT * FROM payments WHERE booking_id = 1;
```

### Issue 3: Status not updating
**Solution:** Check transaction and error handling:
```php
try {
    $conn->begin_transaction();
    // ... queries ...
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
}
```

---

## 📊 Progress Tracking

### Day 1 Progress
- [ ] admin/booking_detail.php created
- [ ] Payment verification implemented
- [ ] admin/bookings.php updated
- [ ] Testing completed

### Day 2 Progress
- [ ] admin/packages.php created
- [ ] admin/package_form.php created
- [ ] CRUD operations working
- [ ] Testing completed

### Day 3 Progress
- [ ] Notification badge added
- [ ] Filter & search added
- [ ] Integration testing completed
- [ ] Bug fixes completed

---

## 🚀 Deployment Checklist

### Before Deploy
- [ ] All tests passed
- [ ] No console errors
- [ ] No PHP errors
- [ ] Database migrations applied
- [ ] Sample data seeded
- [ ] Git committed

### Deploy Steps
```bash
# 1. Backup database
mysqldump -u root explore_kaltim > backup_before_phase1.sql

# 2. Merge to main branch
git checkout main
git merge phase-1-admin-booking

# 3. Test on production
# Access admin panel
# Test all features

# 4. Monitor for issues
# Check error logs
# Check user feedback
```

---

## 📞 Support & Resources

### Documentation
- `FEATURE_PLANNING.md` - Full feature planning
- `PHASE_1_TASKS.md` - Detailed task breakdown
- `BOOKING_FLOW_DIAGRAM.md` - Visual flow diagram

### Code References
- `user/booking_detail.php` - Reference for UI structure
- `admin/destinations.php` - Reference for CRUD operations
- `admin/bookings.php` - Reference for list page

### Help
- Check existing code for patterns
- Use prepared statements for all queries
- Validate input on both client and server
- Use transactions for multiple queries
- Log errors for debugging

---

**Ready to Start?**

1. ✅ Review this guide
2. ✅ Setup environment
3. ✅ Create git branch
4. ✅ Start with Day 1 tasks
5. ✅ Test thoroughly
6. ✅ Deploy with confidence

**Good luck! 🚀**

---

**Document Version:** 1.0  
**Last Updated:** 26 Januari 2026  
**Estimated Time:** 2-3 days
