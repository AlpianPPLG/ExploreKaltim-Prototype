<?php
/**
 * ========================================
 * VIEW BOOKINGS (Admin)
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Handle status updates
if (isset($_POST['update_status'])) {
    $bookingId = (int)$_POST['booking_id'];
    $newStatus = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $bookingId);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Booking status updated successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to update booking status.";
    }
    $stmt->close();
    header("Location: bookings.php");
    exit();
}

// Get filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters
$query = "
    SELECT b.*, u.username, u.email,
           (SELECT p.name FROM booking_details bd JOIN packages p ON bd.package_id = p.id WHERE bd.booking_id = b.id LIMIT 1) as package_name,
           (SELECT COUNT(*) FROM booking_details WHERE booking_id = b.id) as item_count
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE 1=1
";

if ($statusFilter != '') {
    $query .= " AND b.status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if ($searchQuery != '') {
    $searchEscaped = $conn->real_escape_string($searchQuery);
    $query .= " AND (b.booking_code LIKE '%$searchEscaped%' OR u.username LIKE '%$searchEscaped%')";
}

$query .= " ORDER BY b.booking_date DESC";

$result = $conn->query($query);
$bookings = $result->fetch_all(MYSQLI_ASSOC);

// Get pending payment count for notification badge
$pendingResult = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'waiting_payment'");
$pendingCount = $pendingResult->fetch_assoc()['count'];
closeDBConnection($conn);

// Helper for status badges
function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'waiting_payment': return 'bg-orange-100 text-orange-700';
        case 'paid': return 'bg-blue-100 text-blue-700';
        case 'confirmed': return 'bg-indigo-100 text-indigo-700';
        case 'completed': return 'bg-green-100 text-green-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - Explore Kaltim Admin</title>
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
                    <h1 class="text-xl font-display font-bold text-primary-800">Booking Management</h1>
                </div>
                <a href="bookings.php?status=waiting_payment" class="relative">
                    <svg class="w-6 h-6 text-gray-600 hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <?php if ($pendingCount > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            <?php echo $pendingCount; ?>
                        </span>
                    <?php endif; ?>
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

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="<?php echo escapeOutput($searchQuery); ?>" placeholder="Search by booking code or username..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div class="min-w-[180px]">
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="waiting_payment" <?php echo $statusFilter == 'waiting_payment' ? 'selected' : ''; ?>>Waiting Payment</option>
                        <option value="paid" <?php echo $statusFilter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="confirmed" <?php echo $statusFilter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $statusFilter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-semibold transition">
                    Filter
                </button>
                <?php if ($statusFilter || $searchQuery): ?>
                    <a href="bookings.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold transition">
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
                            <th class="px-6 py-4">Booking Info</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Total Amount</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-lg font-medium">No bookings yet</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr class="hover:bg-gray-50 transition <?php echo $booking['status'] == 'waiting_payment' ? 'bg-orange-50' : ''; ?>">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900"><?php echo $booking['booking_code']; ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('d M Y, H:i', strtotime($booking['booking_date'])); ?></div>
                                        <div class="text-xs font-medium text-primary-600 mt-1"><?php echo $booking['package_name'] ?: 'Custom Package'; ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo escapeOutput($booking['username']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo escapeOutput($booking['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">Rp <?php echo number_format($booking['total_amount'], 0, ',', '.'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo getStatusBadge($booking['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="booking_detail.php?id=<?php echo $booking['id']; ?>" class="bg-primary-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-primary-700 transition">
                                                View Detail
                                            </a>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <select name="status" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-primary-500 font-medium">
                                                    <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="waiting_payment" <?php echo $booking['status'] == 'waiting_payment' ? 'selected' : ''; ?>>Waiting Payment</option>
                                                    <option value="paid" <?php echo $booking['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                    <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="bg-primary-600 text-white p-1 rounded hover:bg-primary-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
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
</body>
</html>
