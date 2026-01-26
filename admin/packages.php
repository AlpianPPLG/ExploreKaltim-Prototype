<?php
/**
 * ========================================
 * PACKAGE MANAGEMENT (Admin)
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Handle delete package
if (isset($_POST['delete_package'])) {
    $packageId = (int)$_POST['package_id'];
    
    $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->bind_param("i", $packageId);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Package deleted successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to delete package.";
    }
    $stmt->close();
    header("Location: packages.php");
    exit();
}

// Get filter
$destFilter = isset($_GET['destination']) ? (int)$_GET['destination'] : 0;

// Get all packages with destination info
$query = "
    SELECT p.*, d.name as dest_name, d.slug as dest_slug
    FROM packages p
    JOIN destinations d ON p.destination_id = d.id
    WHERE 1=1
";

if ($destFilter > 0) {
    $query .= " AND p.destination_id = $destFilter";
}

$query .= " ORDER BY p.created_at DESC";

$result = $conn->query($query);
$packages = $result->fetch_all(MYSQLI_ASSOC);

// Get all destinations for filter
$destinations = $conn->query("SELECT id, name FROM destinations ORDER BY name")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Management - Explore Kaltim Admin</title>
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
                    },
                    fontFamily: {
                        'display': ['Montserrat', 'sans-serif'],
                        'body': ['Poppins', 'sans-serif'],
                    },
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
                    <h1 class="text-xl font-display font-bold text-primary-800">Package Management</h1>
                </div>
                <a href="package_form.php" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 font-semibold transition">
                    + Add Package
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <select name="destination" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="0">All Destinations</option>
                        <?php foreach ($destinations as $dest): ?>
                            <option value="<?php echo $dest['id']; ?>" <?php echo $destFilter == $dest['id'] ? 'selected' : ''; ?>>
                                <?php echo escapeOutput($dest['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-semibold transition">
                    Filter
                </button>
                <?php if ($destFilter > 0): ?>
                    <a href="packages.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-4">Package Name</th>
                            <th class="px-6 py-4">Destination</th>
                            <th class="px-6 py-4">Price</th>
                            <th class="px-6 py-4">Duration</th>
                            <th class="px-6 py-4">Stock</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($packages)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-lg font-medium">No packages yet</p>
                                    <a href="package_form.php" class="text-primary-600 hover:text-primary-700 font-semibold mt-2 inline-block">
                                        Create your first package
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($packages as $package): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900"><?php echo escapeOutput($package['name']); ?></div>
                                        <div class="text-xs text-gray-500 line-clamp-1"><?php echo escapeOutput($package['description']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo escapeOutput($package['dest_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-primary-600">Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700"><?php echo escapeOutput($package['duration'] ?: '-'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700"><?php echo $package['stock'] !== null ? $package['stock'] : 'Unlimited'; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo $package['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'; ?>">
                                            <?php echo $package['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="package_form.php?id=<?php echo $package['id']; ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-blue-700 transition">
                                                Edit
                                            </a>
                                            <button onclick="confirmDelete(<?php echo $package['id']; ?>, '<?php echo escapeOutput($package['name']); ?>')" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-display font-bold text-gray-900 mb-4">Delete Package</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete package "<span id="packageName" class="font-semibold"></span>"? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="package_id" id="packageId">
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" name="delete_package" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 font-semibold transition">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            document.getElementById('packageId').value = id;
            document.getElementById('packageName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
