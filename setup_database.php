<?php
/**
 * ========================================
 * DATABASE SETUP SCRIPT
 * ========================================
 * Run this file once to set up the database with proper password hashes
 * Access: http://localhost/ExploreKaltim/setup_database.php
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'explorekaltim');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Explore Kaltim - Database Setup</h2>";
echo "<pre>";

// Create database
echo "Creating database...\n";
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "✓ Database created successfully\n\n";
} else {
    echo "✗ Error creating database: " . $conn->error . "\n\n";
}

// Select database
$conn->select_db(DB_NAME);

// Read and execute SQL file
echo "Executing SQL schema...\n";
$sqlFile = file_get_contents('src/sql/query.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sqlFile)));

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    // Skip the INSERT statements for users (we'll add them with proper hashes)
    if (strpos($statement, "INSERT INTO users") !== false) {
        continue;
    }
    
    if ($conn->query($statement) === TRUE) {
        // Extract table name for better feedback
        if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
            echo "✓ Table '{$matches[1]}' created\n";
        }
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

echo "\n";

// Insert users with proper password hashes
echo "Creating demo users...\n";

// Hash passwords
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
$userPassword = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);

// Admin user
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role, avatar_url) VALUES (?, ?, ?, ?, ?)");
$username = 'admin';
$email = 'admin@explorekaltim.com';
$role = 'admin';
$avatar = 'https://ui-avatars.com/api/?name=Admin&size=200&background=059669&color=fff&bold=true';
$stmt->bind_param("sssss", $username, $email, $adminPassword, $role, $avatar);

if ($stmt->execute()) {
    echo "✓ Admin user created (admin@explorekaltim.com / admin123)\n";
} else {
    echo "✗ Error creating admin: " . $stmt->error . "\n";
}

// Regular user
$username = 'budi';
$email = 'budi@gmail.com';
$role = 'user';
$avatar = 'https://ui-avatars.com/api/?name=Budi&size=200&background=059669&color=fff&bold=true';
$stmt->bind_param("sssss", $username, $email, $userPassword, $role, $avatar);

if ($stmt->execute()) {
    echo "✓ Regular user created (budi@gmail.com / user123)\n";
} else {
    echo "✗ Error creating user: " . $stmt->error . "\n";
}

$stmt->close();

echo "\n";
echo "========================================\n";
echo "Database setup completed!\n";
echo "========================================\n\n";
echo "Demo Credentials:\n";
echo "Admin: admin@explorekaltim.com / admin123\n";
echo "User:  budi@gmail.com / user123\n\n";
echo "You can now:\n";
echo "1. Login at: http://localhost/ExploreKaltim/login.php\n";
echo "2. Register at: http://localhost/ExploreKaltim/register.php\n\n";
echo "⚠️ IMPORTANT: Delete this file (setup_database.php) after setup for security!\n";

$conn->close();

echo "</pre>";
?>
