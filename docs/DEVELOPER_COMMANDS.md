# 💻 Developer Commands - Quick Reference

## 🚀 Setup Commands

### Initial Setup
```bash
# 1. Start XAMPP
# - Start Apache
# - Start MySQL

# 2. Create Database
http://localhost/ExploreKaltim/setup_database.php

# 3. Run Migrations
http://localhost/ExploreKaltim/run_migrations.php

# 4. Seed Data (Optional)
http://localhost/ExploreKaltim/migrate_seed.php
```

---

## 🗄️ Database Commands

### Access phpMyAdmin
```
http://localhost/phpmyadmin
```

### Manual SQL Execution
```sql
-- Use database
USE explorekaltim;

-- Check tables
SHOW TABLES;

-- Check table structure
DESCRIBE payments;
DESCRIBE payment_history;

-- View data
SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 10;
SELECT * FROM payments WHERE payment_status = 'pending';
SELECT * FROM payment_history ORDER BY created_at DESC;

-- Check pending payments count
SELECT COUNT(*) FROM bookings WHERE status = 'waiting_payment';
```

### Backup Database
```bash
# Via phpMyAdmin: Export > SQL > Go

# Or via command line:
mysqldump -u root -p explorekaltim > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
# Via phpMyAdmin: Import > Choose file > Go

# Or via command line:
mysql -u root -p explorekaltim < backup_20260126.sql
```

---

## 🔍 Testing URLs

### Public Pages
```
Landing Page:     http://localhost/ExploreKaltim/
Login:            http://localhost/ExploreKaltim/login.php
Register:         http://localhost/ExploreKaltim/register.php
Destinations:     http://localhost/ExploreKaltim/explorasi.php
Booking:          http://localhost/ExploreKaltim/booking.php
```

### Admin Pages
```
Dashboard:        http://localhost/ExploreKaltim/admin/dashboard.php
Bookings:         http://localhost/ExploreKaltim/admin/bookings.php
Booking Detail:   http://localhost/ExploreKaltim/admin/booking_detail.php?id=1
Packages:         http://localhost/ExploreKaltim/admin/packages.php
Add Package:      http://localhost/ExploreKaltim/admin/package_form.php
Edit Package:     http://localhost/ExploreKaltim/admin/package_form.php?id=1
Destinations:     http://localhost/ExploreKaltim/admin/destinations.php
Users:            http://localhost/ExploreKaltim/admin/users.php
```

### User Pages
```
Dashboard:        http://localhost/ExploreKaltim/user/dashboard.php
My Bookings:      http://localhost/ExploreKaltim/user/bookings.php
Booking Detail:   http://localhost/ExploreKaltim/user/booking_detail.php?id=1
```

### Utility Pages
```
Migrations:       http://localhost/ExploreKaltim/run_migrations.php
Seed Data:        http://localhost/ExploreKaltim/migrate_seed.php
Test Auth:        http://localhost/ExploreKaltim/test_auth.php
```

---

## 🧪 Testing Scenarios

### Test Payment Approval Flow
```bash
# 1. Login as user (budi/user123)
# 2. Create booking
# 3. Upload payment proof
# 4. Logout

# 5. Login as admin (admin/admin123)
# 6. Go to bookings page
# 7. Check notification badge (should show count)
# 8. Click "View Detail" on booking
# 9. Click "Approve Payment"
# 10. Verify success message

# 11. Login as user again
# 12. Go to booking detail
# 13. Verify green success notification
```

### Test Payment Rejection Flow
```bash
# 1. Login as admin
# 2. Go to booking detail with pending payment
# 3. Click "Reject Payment"
# 4. Enter rejection reason
# 5. Submit
# 6. Verify success message

# 7. Login as user
# 8. Go to booking detail
# 9. Verify red rejection notification with reason
# 10. Verify "Upload Ulang" form appears
# 11. Upload new payment proof
# 12. Verify status changes to waiting_payment
```

### Test Package CRUD
```bash
# 1. Login as admin
# 2. Go to packages page
# 3. Click "Add Package"
# 4. Fill form and submit
# 5. Verify package appears in list

# 6. Click "Edit" on package
# 7. Update information
# 8. Submit
# 9. Verify changes saved

# 10. Click "Delete" on package
# 11. Confirm deletion
# 12. Verify package removed
```

---

## 🐛 Debugging Commands

### Check PHP Errors
```php
// Add to top of PHP file for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Or check error log
tail -f /xampp/apache/logs/error_log
```

### Check MySQL Errors
```php
// After query execution
if (!$result) {
    echo "Error: " . $conn->error;
}

// For prepared statements
if (!$stmt->execute()) {
    echo "Error: " . $stmt->error;
}
```

### Debug Session
```php
// Check session data
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Check if user logged in
var_dump(isLoggedIn());
var_dump(isAdmin());
```

### Debug Query
```php
// Print query before execution
echo $query;
exit();

// Check affected rows
echo "Affected rows: " . $conn->affected_rows;

// Check last insert ID
echo "Last ID: " . $conn->insert_id;
```

---

## 📝 Code Snippets

### Create New Migration
```bash
# 1. Create file: migrations/002_your_migration_name.sql

# 2. Add SQL:
USE explorekaltim;

-- Your SQL statements here
ALTER TABLE table_name ADD COLUMN new_column VARCHAR(255);

SELECT 'Migration 002 completed successfully!' as status;

# 3. Run migrations:
http://localhost/ExploreKaltim/run_migrations.php
```

### Add New Admin Page
```php
<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Your code here

closeDBConnection($conn);
?>
```

### Add New User Page
```php
<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require login
requireLogin('../login.php');

$conn = getDBConnection();
$user = getCurrentUser();

// Your code here

closeDBConnection($conn);
?>
```

### Add Success Message
```php
// Set message
$_SESSION['success_msg'] = "Operation successful!";
header("Location: target_page.php");
exit();

// Display message
<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>
```

---

## 🔐 Security Checklist

### Before Deployment
- [ ] Remove debug code (error_reporting, print_r, var_dump)
- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Set secure session settings
- [ ] Validate all user inputs
- [ ] Escape all outputs
- [ ] Use prepared statements
- [ ] Implement CSRF protection
- [ ] Add rate limiting
- [ ] Enable error logging (not display)
- [ ] Backup database
- [ ] Test all features

---

## 📊 Performance Optimization

### Database Optimization
```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_booking_status ON bookings(status);
CREATE INDEX idx_booking_user ON bookings(user_id);
CREATE INDEX idx_payment_status ON payments(payment_status);

-- Analyze table
ANALYZE TABLE bookings;
ANALYZE TABLE payments;

-- Optimize table
OPTIMIZE TABLE bookings;
OPTIMIZE TABLE payments;
```

### Query Optimization
```php
// Use LIMIT for pagination
SELECT * FROM bookings ORDER BY booking_date DESC LIMIT 20 OFFSET 0;

// Use specific columns instead of *
SELECT id, booking_code, status FROM bookings;

// Use JOINs efficiently
SELECT b.*, u.username 
FROM bookings b 
INNER JOIN users u ON b.user_id = u.id;
```

---

## 🔄 Git Commands

### Basic Workflow
```bash
# Check status
git status

# Add files
git add .

# Commit
git commit -m "[FEATURE] Add payment verification"

# Push
git push origin main

# Pull latest
git pull origin main
```

### Branch Management
```bash
# Create new branch
git checkout -b feature/new-feature

# Switch branch
git checkout main

# Merge branch
git merge feature/new-feature

# Delete branch
git branch -d feature/new-feature
```

---

## 📦 Backup & Restore

### Full Backup
```bash
# 1. Backup database
mysqldump -u root -p explorekaltim > backup_db.sql

# 2. Backup files
zip -r backup_files.zip /path/to/ExploreKaltim

# 3. Store backups safely
```

### Restore
```bash
# 1. Restore database
mysql -u root -p explorekaltim < backup_db.sql

# 2. Restore files
unzip backup_files.zip -d /path/to/restore
```

---

## 🎯 Quick Fixes

### Clear Session
```php
// Add to any page temporarily
session_start();
session_destroy();
echo "Session cleared!";
```

### Reset Admin Password
```sql
-- Run in phpMyAdmin
UPDATE users 
SET password = '$2y$12$LQv3c1yycEPICh0k.0uYOeP9rEZiRg7h8J7J7J7J7J7J7J7J7J7J7O' 
WHERE username = 'admin';
-- Password will be: admin123
```

### Fix Permissions (Linux/Mac)
```bash
chmod -R 755 /path/to/ExploreKaltim
chmod -R 777 /path/to/ExploreKaltim/uploads
```

---

**Last Updated:** 26 Januari 2026  
**Version:** 1.0  
**For:** Phase 1 Development
