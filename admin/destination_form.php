<?php
/**
 * ========================================
 * ADMIN DESTINATION FORM PAGE
 * ========================================
 * Create/Edit destinations
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination - Explore Kaltim</title>
    <link rel="icon" type="image/svg+xml" href="../src/assets/icons/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Add New Destination</h1>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">Destination form coming soon!</p>
        </div>
    </div>
</body>
</html>
