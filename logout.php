<?php
/**
 * ========================================
 * LOGOUT PAGE
 * ========================================
 * Secure session destruction
 */

require_once 'config/session.php';

// Destroy session
destroyUserSession();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Redirect to login page
header("Location: /ExploreKaltim/login.php?logout=success");
exit();
?>
