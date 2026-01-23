<?php
/**
 * ========================================
 * USER BOOKINGS PAGE
 * ========================================
 * View user's booking history
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require login
requireLogin('../login.php');

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Explore Kaltim</title>
    <link rel="icon" type="image/svg+xml" href="../src/assets/icons/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">My Bookings</h1>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">No bookings yet. Start exploring destinations!</p>
            <a href="../index.html" class="mt-4 inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Explore Destinations
            </a>
        </div>
    </div>
</body>
</html>
