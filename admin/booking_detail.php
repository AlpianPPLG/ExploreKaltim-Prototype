<?php
/**
 * ========================================
 * BOOKING DETAIL PAGE (Admin)
 * ========================================
 * View complete booking information and manage payment verification
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Get booking ID from URL
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bookingId <= 0) {
    header("Location: bookings.php");
    exit();
}

// Handle payment approval
if (isset($_POST['approve_payment'])) {
    $paymentId = (int)$_POST['payment_id'];
    $adminId = $_SESSION['user_id'];
    
    $conn->begin_transaction();
    
    try {
        // Get current payment status
        $stmt = $conn->prepare("SELECT payment_status FROM payments WHERE id = ? AND booking_id = ?");
        $stmt->bind_param("ii", $paymentId, $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $oldStatus = $payment['payment_status'];
        $stmt->close();
        
        // Update payment status to approved
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'approved', verified_by = ?, paid_at = NOW() WHERE id = ? AND booking_id = ?");
        $stmt->bind_param("iii", $adminId, $paymentId, $bookingId);
        $stmt->execute();
        $stmt->close();
        
        // Update booking status to paid
        $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $stmt->close();
        
        // Log to payment history
        $stmt = $conn->prepare("INSERT INTO payment_history (payment_id, booking_id, old_status, new_status, changed_by, reason) VALUES (?, ?, ?, 'approved', ?, 'Payment approved by admin')");
        $stmt->bind_param("iisi", $paymentId, $bookingId, $oldStatus, $adminId);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        $_SESSION['success_msg'] = "Payment approved successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to approve payment: " . $e->getMessage();
    }
    
    header("Location: booking_detail.php?id=" . $bookingId);
    exit();
}

// Handle payment rejection
if (isset($_POST['reject_payment'])) {
    $paymentId = (int)$_POST['payment_id'];
    $reason = trim($_POST['rejection_reason']);
    $adminId = $_SESSION['user_id'];
    
    if (empty($reason)) {
        $_SESSION['error_msg'] = "Please provide a reason for rejection.";
        header("Location: booking_detail.php?id=" . $bookingId);
        exit();
    }
    
    $conn->begin_transaction();
    
    try {
        // Get current payment status
        $stmt = $conn->prepare("SELECT payment_status FROM payments WHERE id = ? AND booking_id = ?");
        $stmt->bind_param("ii", $paymentId, $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $oldStatus = $payment['payment_status'];
        $stmt->close();
        
        // Update payment status to rejected
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'rejected', rejection_reason = ?, verified_by = ? WHERE id = ? AND booking_id = ?");
        $stmt->bind_param("siii", $reason, $adminId, $paymentId, $bookingId);
        $stmt->execute();
        $stmt->close();
        
        // Update booking status back to pending
        $stmt = $conn->prepare("UPDATE bookings SET status = 'pending' WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $stmt->close();
        
        // Log to payment history
        $stmt = $conn->prepare("INSERT INTO payment_history (payment_id, booking_id, old_status, new_status, changed_by, reason) VALUES (?, ?, ?, 'rejected', ?, ?)");
        $stmt->bind_param("iisis", $paymentId, $bookingId, $oldStatus, $adminId, $reason);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        $_SESSION['success_msg'] = "Payment rejected successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to reject payment: " . $e->getMessage();
    }
    
    header("Location: booking_detail.php?id=" . $bookingId);
    exit();
}

// Get booking with all related data
$query = "
    SELECT b.*, u.username, u.email, u.avatar_url,
           bd.quantity, bd.price_per_unit, bd.travel_date, bd.note, bd.subtotal,
           p.name as package_name, p.description as package_desc,
           d.name as dest_name, d.slug as dest_slug, d.address as dest_address,
           pay.id as payment_id, pay.method, pay.payment_proof, pay.payment_status, 
           pay.rejection_reason, pay.created_at as payment_date, pay.paid_at,
           admin.username as verified_by_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN booking_details bd ON b.id = bd.booking_id
    JOIN packages p ON bd.package_id = p.id
    JOIN destinations d ON p.destination_id = d.id
    LEFT JOIN payments pay ON b.id = pay.booking_id
    LEFT JOIN users admin ON pay.verified_by = admin.id
    WHERE b.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    closeDBConnection($conn);
    header("Location: bookings.php");
    exit();
}

// Get payment history
$historyQuery = "
    SELECT ph.*, u.username as changed_by_name
    FROM payment_history ph
    JOIN users u ON ph.changed_by = u.id
    WHERE ph.booking_id = ?
    ORDER BY ph.created_at DESC
";
$stmt = $conn->prepare($historyQuery);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$paymentHistory = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
        case 'approved': return 'bg-green-100 text-green-700';
        case 'rejected': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - Explore Kaltim Admin</title>
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
                    <a href="bookings.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-display font-bold text-primary-800">Booking Detail</h1>
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

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-display font-bold text-gray-900"><?php echo $booking['booking_code']; ?></h2>
                    <p class="text-sm text-gray-500 mt-1">Booked on <?php echo date('d M Y, H:i', strtotime($booking['booking_date'])); ?></p>
                </div>
                <span class="px-4 py-2 text-sm font-bold rounded-full <?php echo getStatusBadge($booking['status']); ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Customer Information</h3>
                    <div class="flex items-center gap-4">
                        <?php if ($booking['avatar_url']): ?>
                            <img src="<?php echo escapeOutput($booking['avatar_url']); ?>" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-2xl font-bold text-primary-600"><?php echo strtoupper(substr($booking['username'], 0, 1)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo escapeOutput($booking['username']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo escapeOutput($booking['email']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Destination & Package Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Destination & Package</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Destination</p>
                            <p class="font-semibold text-gray-900"><?php echo escapeOutput($booking['dest_name']); ?></p>
                            <p class="text-sm text-gray-600"><?php echo escapeOutput($booking['dest_address']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Package</p>
                            <p class="font-semibold text-gray-900"><?php echo escapeOutput($booking['package_name']); ?></p>
                            <p class="text-sm text-gray-600"><?php echo escapeOutput($booking['package_desc']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Booking Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Travel Date</span>
                            <span class="font-semibold text-gray-900"><?php echo date('d M Y', strtotime($booking['travel_date'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Quantity</span>
                            <span class="font-semibold text-gray-900"><?php echo $booking['quantity']; ?> pax</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Price per Unit</span>
                            <span class="font-semibold text-gray-900">Rp <?php echo number_format($booking['price_per_unit'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-200">
                            <span class="text-lg font-bold text-gray-900">Total Amount</span>
                            <span class="text-lg font-bold text-primary-600">Rp <?php echo number_format($booking['total_amount'], 0, ',', '.'); ?></span>
                        </div>
                        <?php if ($booking['note']): ?>
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-1">Customer Note</p>
                                <p class="text-gray-700"><?php echo escapeOutput($booking['note']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment History -->
                <?php if (!empty($paymentHistory)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Payment History</h3>
                    <div class="space-y-3">
                        <?php foreach ($paymentHistory as $history): ?>
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="w-2 h-2 rounded-full bg-primary-500 mt-2"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">
                                        Status changed from <span class="text-orange-600"><?php echo $history['old_status']; ?></span> 
                                        to <span class="text-green-600"><?php echo $history['new_status']; ?></span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        by <?php echo escapeOutput($history['changed_by_name']); ?> • 
                                        <?php echo date('d M Y, H:i', strtotime($history['created_at'])); ?>
                                    </p>
                                    <?php if ($history['reason']): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo escapeOutput($history['reason']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Payment Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4">Payment Information</h3>
                    
                    <?php if ($booking['payment_id']): ?>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Payment Method</p>
                                <p class="font-semibold text-gray-900"><?php echo escapeOutput($booking['method'] ?: 'Not specified'); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Status</p>
                                <span class="inline-block px-3 py-1 text-xs font-bold rounded-full <?php echo getStatusBadge($booking['payment_status']); ?>">
                                    <?php echo ucfirst($booking['payment_status']); ?>
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Date</p>
                                <p class="font-semibold text-gray-900"><?php echo date('d M Y, H:i', strtotime($booking['payment_date'])); ?></p>
                            </div>
                            <?php if ($booking['paid_at']): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Verified At</p>
                                    <p class="font-semibold text-gray-900"><?php echo date('d M Y, H:i', strtotime($booking['paid_at'])); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($booking['verified_by_name']): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Verified By</p>
                                    <p class="font-semibold text-gray-900"><?php echo escapeOutput($booking['verified_by_name']); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($booking['rejection_reason']): ?>
                                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-sm font-semibold text-red-700 mb-1">Rejection Reason</p>
                                    <p class="text-sm text-red-600"><?php echo escapeOutput($booking['rejection_reason']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Proof -->
                        <?php if ($booking['payment_proof']): ?>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-2">Payment Proof</p>
                                <img src="<?php echo escapeOutput($booking['payment_proof']); ?>" alt="Payment Proof" class="w-full rounded-lg border border-gray-200">
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <?php if ($booking['payment_status'] == 'pending' && $booking['payment_proof']): ?>
                            <div class="mt-6 space-y-3">
                                <form method="POST" class="w-full">
                                    <input type="hidden" name="payment_id" value="<?php echo $booking['payment_id']; ?>">
                                    <button type="submit" name="approve_payment" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 font-semibold transition">
                                        ✓ Approve Payment
                                    </button>
                                </form>
                                
                                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 font-semibold transition">
                                    ✗ Reject Payment
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No payment information yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-display font-bold text-gray-900 mb-4">Reject Payment</h3>
            <form method="POST">
                <input type="hidden" name="payment_id" value="<?php echo $booking['payment_id']; ?>">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason *</label>
                    <textarea name="rejection_reason" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Please provide a clear reason for rejection..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" name="reject_payment" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 font-semibold transition">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
