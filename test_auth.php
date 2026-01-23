<?php
/**
 * ========================================
 * AUTHENTICATION SYSTEM TEST
 * ========================================
 * Quick test to verify all auth components are working
 */

echo "<h1>🔐 Authentication System Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .test { padding: 10px; margin: 10px 0; border-radius: 5px; }
    .pass { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .fail { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    h2 { color: #333; margin-top: 30px; }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>";

// Test 1: Check if config files exist
echo "<h2>1. Configuration Files</h2>";
$configFiles = [
    'config/database.php',
    'config/session.php',
    'config/security.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "<div class='test pass'>✓ {$file} exists</div>";
    } else {
        echo "<div class='test fail'>✗ {$file} missing</div>";
    }
}

// Test 2: Check if auth pages exist
echo "<h2>2. Authentication Pages</h2>";
$authPages = [
    'register.php',
    'login.php',
    'logout.php'
];

foreach ($authPages as $page) {
    if (file_exists($page)) {
        echo "<div class='test pass'>✓ {$page} exists</div>";
    } else {
        echo "<div class='test fail'>✗ {$page} missing</div>";
    }
}

// Test 3: Check if dashboard pages exist
echo "<h2>3. Dashboard Pages</h2>";
$dashboardPages = [
    'admin/dashboard.php',
    'user/dashboard.php'
];

foreach ($dashboardPages as $page) {
    if (file_exists($page)) {
        echo "<div class='test pass'>✓ {$page} exists</div>";
    } else {
        echo "<div class='test fail'>✗ {$page} missing</div>";
    }
}

// Test 4: Database connection
echo "<h2>4. Database Connection</h2>";
try {
    require_once 'config/database.php';
    $conn = getDBConnection();
    echo "<div class='test pass'>✓ Database connection successful</div>";
    
    // Check if database exists
    $result = $conn->query("SELECT DATABASE()");
    $db = $result->fetch_row()[0];
    if ($db === 'explorekaltim') {
        echo "<div class='test pass'>✓ Connected to 'explorekaltim' database</div>";
    } else {
        echo "<div class='test fail'>✗ Wrong database: {$db}</div>";
    }
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<div class='test pass'>✓ 'users' table exists</div>";
        
        // Check if demo users exist
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $result->fetch_assoc()['count'];
        echo "<div class='test pass'>✓ Found {$count} user(s) in database</div>";
    } else {
        echo "<div class='test fail'>✗ 'users' table not found</div>";
        echo "<div class='test info'>ℹ Run setup_database.php to create tables</div>";
    }
    
    closeDBConnection($conn);
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Database connection failed: " . $e->getMessage() . "</div>";
    echo "<div class='test info'>ℹ Make sure XAMPP MySQL is running</div>";
}

// Test 5: Security functions
echo "<h2>5. Security Functions</h2>";
try {
    require_once 'config/security.php';
    
    // Test password hashing
    $testPassword = 'Test123!';
    $hash = hashPassword($testPassword);
    if (verifyPassword($testPassword, $hash)) {
        echo "<div class='test pass'>✓ Password hashing and verification working</div>";
    } else {
        echo "<div class='test fail'>✗ Password verification failed</div>";
    }
    
    // Test email validation
    if (validateEmail('test@example.com')) {
        echo "<div class='test pass'>✓ Email validation working</div>";
    } else {
        echo "<div class='test fail'>✗ Email validation failed</div>";
    }
    
    // Test password strength validation
    if (validatePassword('Test123!')) {
        echo "<div class='test pass'>✓ Password strength validation working</div>";
    } else {
        echo "<div class='test fail'>✗ Password strength validation failed</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Security functions error: " . $e->getMessage() . "</div>";
}

// Test 6: Session management
echo "<h2>6. Session Management</h2>";
try {
    require_once 'config/session.php';
    echo "<div class='test pass'>✓ Session management loaded</div>";
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<div class='test pass'>✓ Session started successfully</div>";
    } else {
        echo "<div class='test fail'>✗ Session not active</div>";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Session error: " . $e->getMessage() . "</div>";
}

// Summary
echo "<h2>📋 Summary</h2>";
echo "<div class='test info'>";
echo "<strong>Next Steps:</strong><br>";
echo "1. If database tables don't exist, run: <a href='setup_database.php'>setup_database.php</a><br>";
echo "2. Test registration: <a href='register.php'>register.php</a><br>";
echo "3. Test login: <a href='login.php'>login.php</a><br>";
echo "4. View admin dashboard: <a href='admin/dashboard.php'>admin/dashboard.php</a><br>";
echo "5. View user dashboard: <a href='user/dashboard.php'>user/dashboard.php</a><br>";
echo "</div>";

echo "<div class='test info'>";
echo "<strong>Demo Credentials:</strong><br>";
echo "Admin: admin@explorekaltim.com / admin123<br>";
echo "User: budi@gmail.com / user123<br>";
echo "</div>";

echo "<div class='test info'>";
echo "<strong>⚠️ Security Note:</strong><br>";
echo "Delete this test file (test_auth.php) and setup_database.php after testing!<br>";
echo "</div>";
?>
