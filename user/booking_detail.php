<?php
/**
 * ========================================
 * DETAIL BOOKING & PEMBAYARAN
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require login
requireLogin('../login.php');

$conn = getDBConnection();
$user = getCurrentUser();

$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bookingId === 0) {
    header("Location: dashboard.php");
    exit();
}

// Get Booking & Detail data
// Note: We check user_id to ensure the user can only see their own bookings
$query = "
    SELECT b.*, bd.quantity, bd.price_per_unit, bd.subtotal, bd.travel_date, bd.note,
           p.name as package_name, d.name as dest_name,
           pay.id as payment_id, pay.payment_status, pay.method, pay.payment_proof, pay.rejection_reason
    FROM bookings b
    JOIN booking_details bd ON b.id = bd.booking_id
    JOIN packages p ON bd.package_id = p.id
    JOIN destinations d ON p.destination_id = d.id
    LEFT JOIN payments pay ON b.id = pay.booking_id
    WHERE b.id = $bookingId AND b.user_id = {$user['id']}
    LIMIT 1
";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$booking = $result->fetch_assoc();

// Handle Payment Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_payment'])) {
    $method = trim($_POST['method']);
    $paymentProof = trim($_POST['payment_proof_url']); // For prototype we use URL
    
    // Validation
    $paymentErrors = [];
    
    if (empty($method)) {
        $paymentErrors[] = "Metode pembayaran harus dipilih.";
    }
    
    if (empty($paymentProof)) {
        $paymentErrors[] = "URL bukti pembayaran harus diisi.";
    } elseif (!filter_var($paymentProof, FILTER_VALIDATE_URL)) {
        $paymentErrors[] = "URL bukti pembayaran tidak valid.";
    }
    
    if (empty($paymentErrors)) {
        // Process Payment
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, method, payment_proof, payment_status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("idss", $bookingId, $booking['total_amount'], $method, $paymentProof);
        
        if ($stmt->execute()) {
            // Update booking status
            $conn->query("UPDATE bookings SET status = 'waiting_payment' WHERE id = $bookingId");
            $_SESSION['success_msg'] = "Bukti pembayaran berhasil diupload! Admin akan segera memverifikasi.";
            header("Location: booking_detail.php?id=" . $bookingId);
            exit();
        } else {
            $paymentErrors[] = "Gagal mengupload bukti pembayaran. Silakan coba lagi.";
        }
    }
}

closeDBConnection($conn);

// Helper for status classes
function getStatusLabel($status) {
    switch ($status) {
        case 'pending': return ['label' => 'Menunggu Pembayaran', 'class' => 'bg-yellow-100 text-yellow-700'];
        case 'waiting_payment': return ['label' => 'Menunggu Verifikasi', 'class' => 'bg-orange-100 text-orange-700'];
        case 'paid': return ['label' => 'Terbayar', 'class' => 'bg-blue-100 text-blue-700'];
        case 'confirmed': return ['label' => 'Terkonfirmasi', 'class' => 'bg-indigo-100 text-indigo-700'];
        case 'completed': return ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-700'];
        case 'cancelled': return ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'];
        default: return ['label' => $status, 'class' => 'bg-gray-100 text-gray-700'];
    }
}

$status = getStatusLabel($booking['status']);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan <?php echo $booking['booking_code']; ?> - Explore Kaltim</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9f6',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                        },
                        secondary: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        }
                    },
                    fontFamily: {
                        'heading': ['Montserrat', 'sans-serif'],
                        'display': ['Montserrat', 'sans-serif'],
                        'body': ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="font-body antialiased bg-gray-50">
    <!-- Navbar Simulation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <a href="dashboard.php" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i class="fas fa-arrow-left text-gray-500"></i>
                    </a>
                    <h1 class="text-lg font-heading font-bold text-gray-900">Detail Pesanan</h1>
                </div>
                <div class="text-xs font-bold text-primary-700 bg-primary-100 px-3 py-1 rounded-full uppercase tracking-widest">
                    Explore Kaltim
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="container mx-auto px-4 lg:px-8 max-w-4xl">
            
            <?php if (isset($_SESSION['booking_success'])): ?>
                <div class="mb-8 p-6 bg-primary-50 border border-primary-100 text-primary-800 rounded-3xl flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center shrink-0">
                        <i class="fas fa-check-circle text-2xl text-primary-600"></i>
                    </div>
                    <p class="font-bold"><?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="mb-8 p-6 bg-primary-50 border border-primary-100 text-primary-800 rounded-3xl">
                    <p class="font-bold flex items-center gap-3">
                        <i class="fas fa-info-circle"></i>
                        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($booking['payment_status'] === 'approved'): ?>
                <div class="mb-8 p-6 bg-green-50 border border-green-200 text-green-800 rounded-3xl">
                    <p class="font-bold flex items-center gap-3">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span>Pembayaran Anda telah disetujui! Terima kasih telah menggunakan layanan kami.</span>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($booking['payment_status'] === 'rejected' && $booking['rejection_reason']): ?>
                <div class="mb-8 p-6 bg-red-50 border border-red-200 text-red-800 rounded-3xl">
                    <p class="font-bold flex items-center gap-3 mb-3">
                        <i class="fas fa-times-circle text-2xl"></i>
                        <span>Pembayaran Anda ditolak</span>
                    </p>
                    <p class="text-sm ml-9"><strong>Alasan:</strong> <?php echo escapeOutput($booking['rejection_reason']); ?></p>
                    <p class="text-sm ml-9 mt-2">Silakan upload bukti pembayaran yang baru.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($paymentErrors) && !empty($paymentErrors)): ?>
                <div class="mb-8 p-6 bg-red-50 border border-red-200 text-red-700 rounded-3xl">
                    <p class="font-bold mb-2 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        Terjadi Kesalahan:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <?php foreach ($paymentErrors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Main Invoice Info -->
                <div class="md:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-10">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">Invoice Number</p>
                                    <h2 class="text-2xl font-heading font-extrabold text-gray-900"><?php echo $booking['booking_code']; ?></h2>
                                </div>
                                <span class="px-4 py-1.5 rounded-full text-xs font-bold <?php echo $status['class']; ?>">
                                    <?php echo $status['label']; ?>
                                </span>
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-primary-600">
                                        <i class="fas fa-map-location-dot"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Destinasi</p>
                                        <p class="font-bold text-gray-900"><?php echo $booking['dest_name']; ?></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 border border-gray-100 rounded-2xl">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Paket</p>
                                        <p class="text-sm font-bold text-gray-700"><?php echo $booking['package_name']; ?></p>
                                    </div>
                                    <div class="p-4 border border-gray-100 rounded-2xl">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Tanggal Kunjungan</p>
                                        <p class="text-sm font-bold text-gray-700"><?php echo date('d M Y', strtotime($booking['travel_date'])); ?></p>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-100 space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500"><?php echo $booking['quantity']; ?> x Paket Wisata</span>
                                        <span class="font-bold text-gray-700">Rp <?php echo number_format($booking['subtotal'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="flex justify-between text-lg pt-4 border-t border-gray-100">
                                        <span class="font-heading font-bold text-gray-900">Total</span>
                                        <span class="font-heading font-extrabold text-primary-800">Rp <?php echo number_format($booking['total_amount'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Note -->
                    <?php if (!empty($booking['note'])): ?>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm leading-relaxed">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Catatan Kamu</h4>
                            <p class="text-sm text-gray-600 italic">"<?php echo escapeOutput($booking['note']); ?>"</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Payment Section -->
                <div class="md:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        
                        <?php if ($booking['status'] === 'pending' || $booking['payment_status'] === 'rejected'): ?>
                            <!-- Payment Instruction -->
                            <div class="bg-white rounded-3xl shadow-lg border border-primary-100 p-8 overflow-hidden relative">
                                <div class="absolute top-0 left-0 w-2 h-full bg-primary-600"></div>
                                <h3 class="text-xl font-heading font-bold text-gray-900 mb-6">
                                    <?php echo $booking['payment_status'] === 'rejected' ? 'Upload Ulang Bukti Bayar' : 'Bayar Sekarang'; ?>
                                </h3>
                                
                                <div class="space-y-6 mb-8">
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Transfer ke Rekening</p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-6 bg-blue-800 rounded flex items-center justify-center text-[10px] text-white font-bold italic">BCA</div>
                                            <p class="font-bold text-gray-800 tracking-wider">123-4567-890</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">a/n Explore Kaltim Official</p>
                                    </div>

                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">E-Wallet (OVO/DANA/GoPay)</p>
                                        <p class="font-bold text-gray-800 tracking-wider">0812-3456-7890</p>
                                    </div>
                                </div>

                                <form method="POST" class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Metode Pembayaran</label>
                                        <select name="method" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 transition">
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="OVO">OVO</option>
                                            <option value="DANA">DANA</option>
                                            <option value="GoPay">GoPay</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">URL Bukti Bayar / Referensi</label>
                                        <input type="url" name="payment_proof_url" required placeholder="https://imgur.com/screenshot.jpg"
                                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 transition">
                                    </div>
                                    <button type="submit" name="upload_payment" class="w-full py-4 bg-primary-800 text-white font-bold rounded-2xl hover:bg-primary-700 transition shadow-xl shadow-primary-800/20 mt-4">
                                        Konfirmasi Pembayaran
                                    </button>
                                </form>
                            </div>
                        <?php elseif ($booking['status'] === 'waiting_payment'): ?>
                            <div class="bg-orange-50 p-8 rounded-3xl border border-orange-100 text-center">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                    <i class="fas fa-hourglass-half text-orange-500 text-2xl animate-pulse"></i>
                                </div>
                                <h4 class="font-bold text-orange-800 mb-2">Sedang Diverifikasi</h4>
                                <p class="text-sm text-orange-600 leading-relaxed">Admin sedang memeriksa pembayaran Kamu. Update akan muncul di sini segera.</p>
                            </div>
                        <?php elseif (in_array($booking['status'], ['paid', 'confirmed', 'completed']) || $booking['payment_status'] === 'approved'): ?>
                            <div class="bg-primary-50 p-8 rounded-3xl border border-primary-100 text-center">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                    <i class="fas fa-check-double text-primary-600 text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-primary-800 mb-2">Pembayaran Berhasil!</h4>
                                <p class="text-sm text-primary-600 mb-6">Nikmati perjalanan seru Kamu bersama Explore Kaltim.</p>
                                <button onclick="window.print()" class="w-full py-3 bg-white text-primary-700 font-bold rounded-2xl border border-primary-200 hover:bg-primary-100 transition">
                                    Cetak Tiket / Invoice
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-20 py-12 text-center text-gray-400 text-xs">
        &copy; 2024 Explore Kaltim. Booking Platform Profesional.
    </footer>
</body>
</html>
