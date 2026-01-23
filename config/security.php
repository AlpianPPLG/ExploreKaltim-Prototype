<?php
/**
 * ========================================
 * SECURITY FUNCTIONS
 * ========================================
 * Input validation, sanitization, and CSRF protection
 */

// Generate CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate password strength
function validatePassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate random avatar URL
function generateDefaultAvatar($username) {
    // Using UI Avatars service
    $name = urlencode($username);
    return "https://ui-avatars.com/api/?name={$name}&size=200&background=059669&color=fff&bold=true";
}

// Prevent XSS
function escapeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Rate limiting for login attempts
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) {
    // 5 attempts per 15 minutes
    $key = 'login_attempts_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 0,
            'first_attempt' => time()
        ];
    }
    
    $attempts = $_SESSION[$key];
    
    // Reset if time window has passed
    if (time() - $attempts['first_attempt'] > $timeWindow) {
        $_SESSION[$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    // Check if max attempts exceeded
    if ($attempts['count'] >= $maxAttempts) {
        return false;
    }
    
    // Increment attempt count
    $_SESSION[$key]['count']++;
    return true;
}

// Get remaining lockout time
function getRemainingLockoutTime($identifier, $timeWindow = 900) {
    $key = 'login_attempts_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        return 0;
    }
    
    $attempts = $_SESSION[$key];
    $elapsed = time() - $attempts['first_attempt'];
    $remaining = $timeWindow - $elapsed;
    
    return max(0, $remaining);
}

// Reset rate limit
function resetRateLimit($identifier) {
    $key = 'login_attempts_' . md5($identifier);
    unset($_SESSION[$key]);
}
?>
