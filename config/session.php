<?php
/**
 * ========================================
 * SESSION MANAGEMENT
 * ========================================
 * Secure session handling for authentication
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    
    session_start();
}

// Regenerate session ID to prevent session fixation
function regenerateSession() {
    session_regenerate_id(true);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

// Check if user is admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? 'user',
        'avatar_url' => $_SESSION['avatar_url'] ?? null
    ];
}

// Set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['avatar_url'] = $user['avatar_url'];
    $_SESSION['login_time'] = time();
    
    regenerateSession();
}

// Destroy user session
function destroyUserSession() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

// Redirect if not logged in
function requireLogin($redirectTo = '/ExploreKaltim/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
}

// Redirect if not admin
function requireAdmin($redirectTo = '/ExploreKaltim/index.html') {
    if (!isAdmin()) {
        header("Location: $redirectTo");
        exit();
    }
}

// Redirect if already logged in
function redirectIfLoggedIn($userRedirect = '/ExploreKaltim/user/dashboard.php', $adminRedirect = '/ExploreKaltim/admin/dashboard.php') {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header("Location: $adminRedirect");
        } else {
            header("Location: $userRedirect");
        }
        exit();
    }
}
?>
