# 🔐 Authentication System Setup Guide

## ✅ Task 3.1 - Authentication System Implementation

### Features Implemented:

#### ✅ Register (Buat Akun)
- Form input: Username, Email, Password
- Role Selection: User can choose to register as 'User' (Tourist) or 'Admin'
- Default avatar_url assignment using UI Avatars
- Password validation (min 8 chars, uppercase, lowercase, number)
- Email validation
- Username uniqueness check
- CSRF protection
- Input sanitization

#### ✅ Login
- Email & Password validation
- Session management with secure settings
- Admin vs User redirection
- Rate limiting (5 attempts per 15 minutes)
- Remember me functionality
- CSRF protection
- Secure password hashing with bcrypt

#### ✅ Logout
- Secure session destruction
- Cookie cleanup
- Redirect to login page

---

## 📁 Files Created

### Configuration Files:
- `config/database.php` - Database connection management
- `config/session.php` - Session handling and authentication helpers
- `config/security.php` - Security functions (CSRF, validation, sanitization)

### Authentication Pages:
- `register.php` - User registration page
- `login.php` - User login page
- `logout.php` - Logout handler

### Dashboard Pages:
- `admin/dashboard.php` - Admin dashboard with statistics
- `user/dashboard.php` - User dashboard with profile

### Placeholder Pages (for future development):
- `admin/users.php` - User management
- `admin/destinations.php` - Destination management
- `admin/bookings.php` - Booking management
- `admin/destination_form.php` - Add/Edit destination form
- `user/bookings.php` - User booking history
- `user/review.php` - Write reviews

### Setup Script:
- `setup_database.php` - Database initialization script

---

## 🚀 Installation Steps

### 1. Start XAMPP
- Start Apache and MySQL services

### 2. Setup Database
Open your browser and navigate to:
```
http://localhost/ExploreKaltim/setup_database.php
```

This will:
- Create the `explorekaltim` database
- Create all required tables
- Insert demo users with proper password hashes

### 3. Delete Setup File (Security)
After successful setup, delete the setup file:
```
ExploreKaltim/setup_database.php
```

---

## 🔑 Demo Credentials

### Admin Account:
- **Email:** admin@explorekaltim.com
- **Password:** admin123

### User Account:
- **Email:** budi@gmail.com
- **Password:** user123

---

## 🌐 Access URLs

### Public Pages:
- Homepage: `http://localhost/ExploreKaltim/index.html`
- Login: `http://localhost/ExploreKaltim/login.php`
- Register: `http://localhost/ExploreKaltim/register.php`

### User Pages (requires login):
- Dashboard: `http://localhost/ExploreKaltim/user/dashboard.php`
- My Bookings: `http://localhost/ExploreKaltim/user/bookings.php`
- Write Review: `http://localhost/ExploreKaltim/user/review.php`

### Admin Pages (requires admin login):
- Dashboard: `http://localhost/ExploreKaltim/admin/dashboard.php`
- Manage Users: `http://localhost/ExploreKaltim/admin/users.php`
- Manage Destinations: `http://localhost/ExploreKaltim/admin/destinations.php`
- Manage Bookings: `http://localhost/ExploreKaltim/admin/bookings.php`

---

## 🔒 Security Features Implemented

### 1. Password Security
- Bcrypt hashing with cost factor 12
- Password strength validation
- Secure password verification

### 2. Session Security
- HTTP-only cookies
- Session regeneration on login
- Secure session settings
- Session timeout handling

### 3. CSRF Protection
- Token generation and verification
- Form protection against CSRF attacks

### 4. Input Validation & Sanitization
- Email validation
- Password strength validation
- XSS prevention
- SQL injection prevention (prepared statements)

### 5. Rate Limiting
- Login attempt limiting (5 attempts per 15 minutes)
- Lockout mechanism with countdown

### 6. Access Control
- Role-based access (Admin/User)
- Protected routes with authentication checks
- Automatic redirection based on role

---

## 📊 Database Schema

The authentication system uses the following tables:
- `users` - User accounts with roles
- `regencies` - Regions in East Kalimantan
- `categories` - Tourism categories
- `destinations` - Tourist destinations
- `destination_galleries` - Destination images
- `packages` - Tour packages
- `bookings` - Booking transactions
- `booking_details` - Booking items
- `payments` - Payment records
- `reviews` - User reviews

---

## 🧪 Testing the Authentication System

### Test Registration:
1. Go to `register.php`
2. Fill in the form with valid data
3. Select role (User or Admin)
4. Submit and verify success message
5. Check redirect to login page

### Test Login:
1. Go to `login.php`
2. Enter demo credentials
3. Verify redirect to appropriate dashboard
4. Check session persistence

### Test Logout:
1. Click logout link
2. Verify session destruction
3. Try accessing protected pages (should redirect to login)

### Test Rate Limiting:
1. Try logging in with wrong password 5 times
2. Verify lockout message
3. Wait 15 minutes or clear session to reset

### Test Role-Based Access:
1. Login as user
2. Try accessing admin pages (should redirect)
3. Login as admin
4. Verify access to admin pages

---

## 🐛 Troubleshooting

### Database Connection Error:
- Check XAMPP MySQL is running
- Verify database credentials in `config/database.php`
- Ensure database exists

### Session Not Working:
- Check PHP session settings
- Verify session directory is writable
- Clear browser cookies

### Login Fails:
- Verify password hashes are correct
- Check database user records
- Review error messages

### Page Not Found:
- Check file paths are correct
- Verify XAMPP htdocs directory
- Check URL spelling

---

## 📝 Next Steps (Phase 2)

After authentication is working, proceed with:
- [ ] Admin CRUD for Regencies, Categories, Destinations
- [ ] Public Explore Pages
- [ ] Destination Detail Page
- [ ] Booking Flow Implementation

---

## 💡 Notes

- All passwords are hashed using bcrypt
- Sessions are secure with HTTP-only cookies
- CSRF tokens protect all forms
- Input is sanitized to prevent XSS
- Prepared statements prevent SQL injection
- Rate limiting prevents brute force attacks

---

## ⚠️ Security Reminders

1. **Delete setup_database.php after setup**
2. Change default passwords in production
3. Enable HTTPS in production
4. Update session.cookie_secure to 1 with HTTPS
5. Use strong database passwords
6. Keep PHP and MySQL updated
7. Review error logs regularly

---

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section
2. Review PHP error logs in XAMPP
3. Check browser console for JavaScript errors
4. Verify database structure matches schema

---

**Status:** ✅ Task 3.1 Authentication System - COMPLETED

All authentication features are implemented and ready for testing!
