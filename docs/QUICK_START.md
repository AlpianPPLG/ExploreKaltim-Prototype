# 🚀 Quick Start Guide - Explore Kaltim Authentication

## ⚡ 3-Minute Setup

### Step 1: Start XAMPP (30 seconds)
```
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Wait for green indicators
```

### Step 2: Setup Database (1 minute)
```
1. Open browser
2. Go to: http://localhost/ExploreKaltim/setup_database.php
3. Wait for "Database setup completed!" message
4. Note the demo credentials shown
```

### Step 3: Test Authentication (1 minute)
```
1. Go to: http://localhost/ExploreKaltim/test_auth.php
2. Verify all tests show green checkmarks ✓
3. Click on the test links to verify pages load
```

### Step 4: Login & Explore (30 seconds)
```
1. Go to: http://localhost/ExploreKaltim/login.php
2. Use demo credentials:
   - Admin: admin@explorekaltim.com / admin123
   - User: budi@gmail.com / user123
3. Explore the dashboard!
```

---

## 🎯 What You Can Do Now

### As Admin:
- ✅ View statistics dashboard
- ✅ Access admin panel
- ✅ See user count, destinations, bookings, revenue
- ⏳ Manage users (coming in Phase 2)
- ⏳ Manage destinations (coming in Phase 2)
- ⏳ Manage bookings (coming in Phase 2)

### As User:
- ✅ View personal dashboard
- ✅ See booking statistics
- ✅ Access quick actions
- ⏳ Make bookings (coming in Phase 2)
- ⏳ View booking history (coming in Phase 2)
- ⏳ Write reviews (coming in Phase 2)

### As Guest:
- ✅ Register new account
- ✅ Choose role (User/Admin)
- ✅ Login to existing account
- ✅ View homepage

---

## 🔗 Important URLs

### Public Access:
- Homepage: `http://localhost/ExploreKaltim/index.html`
- Login: `http://localhost/ExploreKaltim/login.php`
- Register: `http://localhost/ExploreKaltim/register.php`

### Admin Panel (requires admin login):
- Dashboard: `http://localhost/ExploreKaltim/admin/dashboard.php`
- Users: `http://localhost/ExploreKaltim/admin/users.php`
- Destinations: `http://localhost/ExploreKaltim/admin/destinations.php`
- Bookings: `http://localhost/ExploreKaltim/admin/bookings.php`

### User Panel (requires login):
- Dashboard: `http://localhost/ExploreKaltim/user/dashboard.php`
- Bookings: `http://localhost/ExploreKaltim/user/bookings.php`
- Reviews: `http://localhost/ExploreKaltim/user/review.php`

### Setup & Testing:
- Setup: `http://localhost/ExploreKaltim/setup_database.php`
- Test: `http://localhost/ExploreKaltim/test_auth.php`

---

## 🔑 Demo Credentials

### Admin Account
```
Email: admin@explorekaltim.com
Password: admin123
```

### User Account
```
Email: budi@gmail.com
Password: user123
```

---

## ✅ Quick Test Checklist

### Registration Test:
- [ ] Go to register.php
- [ ] Fill in: username, email, password
- [ ] Select role (User or Admin)
- [ ] Click "Create Account"
- [ ] See success message
- [ ] Redirect to login page

### Login Test:
- [ ] Go to login.php
- [ ] Enter demo credentials
- [ ] Click "Login"
- [ ] Redirect to appropriate dashboard
- [ ] See welcome message with avatar

### Dashboard Test:
- [ ] See statistics cards
- [ ] Click quick action buttons
- [ ] Navigate between pages
- [ ] All links work correctly

### Logout Test:
- [ ] Click "Logout" link
- [ ] Redirect to login page
- [ ] Try accessing dashboard (should redirect to login)
- [ ] Login again works correctly

### Security Test:
- [ ] Try accessing admin page as user (should redirect)
- [ ] Try accessing dashboard without login (should redirect)
- [ ] Try wrong password 5 times (should show lockout)
- [ ] CSRF protection works (forms have tokens)

---

## 🐛 Common Issues & Solutions

### Issue: "Database connection failed"
**Solution:**
- Check XAMPP MySQL is running (green indicator)
- Verify database credentials in `config/database.php`
- Run `setup_database.php` again

### Issue: "Table 'users' doesn't exist"
**Solution:**
- Run `setup_database.php`
- Check for error messages during setup
- Verify database 'explorekaltim' exists in phpMyAdmin

### Issue: "Login fails with correct password"
**Solution:**
- Run `setup_database.php` again to regenerate password hashes
- Clear browser cookies
- Check if user exists in database

### Issue: "Page not found"
**Solution:**
- Check URL spelling
- Verify file exists in correct folder
- Check XAMPP htdocs path
- Restart Apache in XAMPP

### Issue: "Session not working"
**Solution:**
- Clear browser cookies
- Check PHP session settings
- Restart Apache in XAMPP
- Try different browser

---

## 🔒 Security Checklist

### Before Production:
- [ ] Delete `setup_database.php`
- [ ] Delete `test_auth.php`
- [ ] Change demo passwords
- [ ] Enable HTTPS
- [ ] Update `session.cookie_secure` to 1
- [ ] Use strong database password
- [ ] Review error logging
- [ ] Set up database backups
- [ ] Update PHP and MySQL
- [ ] Configure firewall

---

## 📚 Documentation Files

Need more details? Check these files:

1. **AUTH_SETUP_README.md** - Complete setup guide
2. **TASK_3.1_COMPLETED.md** - Full feature list
3. **FILE_STRUCTURE.md** - File organization
4. **QUICK_START.md** - This file

---

## 🎓 Learning Resources

### Understanding the Code:
- `config/database.php` - Learn database connections
- `config/session.php` - Learn session management
- `config/security.php` - Learn security best practices
- `login.php` - Learn authentication flow
- `register.php` - Learn user registration

### Key Concepts:
- **CSRF Protection:** Prevents cross-site request forgery
- **Password Hashing:** Bcrypt for secure password storage
- **Session Management:** Secure user state tracking
- **Rate Limiting:** Prevents brute force attacks
- **Role-Based Access:** Different permissions for admin/user

---

## 🎯 Next Steps

### Immediate:
1. ✅ Complete setup (3 minutes)
2. ✅ Test all features (5 minutes)
3. ✅ Delete setup files (1 minute)
4. ✅ Read documentation (10 minutes)

### Phase 2 Development:
1. ⏳ Admin CRUD for destinations
2. ⏳ Public explore pages
3. ⏳ Booking system
4. ⏳ Payment integration
5. ⏳ Review system

---

## 💡 Pro Tips

1. **Use Chrome DevTools** to inspect network requests and debug
2. **Check PHP error logs** in XAMPP for detailed error messages
3. **Use phpMyAdmin** to view database tables and data
4. **Test in incognito mode** to avoid cookie/cache issues
5. **Keep XAMPP logs open** to monitor server activity

---

## 📞 Need Help?

### Check These First:
1. Error messages in browser
2. PHP error logs in XAMPP
3. Browser console (F12)
4. Database in phpMyAdmin
5. File permissions

### Documentation:
- AUTH_SETUP_README.md - Setup issues
- TASK_3.1_COMPLETED.md - Feature questions
- FILE_STRUCTURE.md - File organization

---

## ✨ Success Indicators

You'll know everything is working when:
- ✅ All test_auth.php checks are green
- ✅ Login redirects to correct dashboard
- ✅ Statistics show on admin dashboard
- ✅ Logout works and redirects properly
- ✅ Registration creates new users
- ✅ Role-based access control works

---

## 🎉 Congratulations!

If you've completed all steps, you now have:
- ✅ Fully functional authentication system
- ✅ Secure password hashing
- ✅ CSRF protection
- ✅ Rate limiting
- ✅ Role-based access control
- ✅ Admin and user dashboards
- ✅ Professional UI with Tailwind CSS

**You're ready for Phase 2 development!** 🚀

---

**Last Updated:** January 23, 2026
**Version:** 1.0
**Status:** Task 3.1 Complete ✅
