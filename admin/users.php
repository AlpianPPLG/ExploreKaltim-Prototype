<?php
/**
 * ========================================
 * MANAGE USERS (Admin)
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Update Role
if (isset($_POST['update_role'])) {
    $targetUserId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];
    
    // Safety check: Don't let admin demote themselves or change other admins if multiple exist (optional rule)
    $currentUser = getCurrentUser();
    if ($targetUserId !== $currentUser['id']) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $targetUserId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_msg'] = "User role updated.";
    }
    header("Location: users.php");
    exit();
}

// Delete User
if (isset($_GET['delete'])) {
    $targetUserId = (int)$_GET['delete'];
    $currentUser = getCurrentUser();
    
    if ($targetUserId !== $currentUser['id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $targetUserId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_msg'] = "User deleted.";
    }
    header("Location: users.php");
    exit();
}

$query = "SELECT id, username, email, role, avatar_url FROM users ORDER BY role ASC, username ASC";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Explore Kaltim Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                        }
                    }
                },
            },
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="font-body antialiased bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="dashboard.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-display font-bold text-primary-800">User Management</h1>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo escapeOutput($u['avatar_url']); ?>" alt="" class="w-8 h-8 rounded-full">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo escapeOutput($u['username']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo escapeOutput($u['email']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                        <?php echo ucfirst($u['role']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <select name="role" class="text-xs border rounded p-1">
                                                <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                            <button type="submit" name="update_role" class="text-xs bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">Update</button>
                                        </form>
                                        <a href="?delete=<?php echo $u['id']; ?>" class="text-red-600 hover:text-red-900 text-sm font-medium" onclick="return confirm('Delete this user?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
