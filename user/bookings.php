<?php
/**
 * ========================================
 * RIWAYAT BOOKING (My Bookings)
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require login
requireLogin('../login.php');

$conn = getDBConnection();
$user = getCurrentUser();

$query = "
    SELECT b.*, 
           (SELECT p.name FROM booking_details bd JOIN packages p ON bd.package_id = p.id WHERE bd.booking_id = b.id LIMIT 1) as package_name,
           (SELECT d.name FROM booking_details bd JOIN packages p ON bd.package_id = p.id JOIN destinations d ON p.destination_id = d.id WHERE bd.booking_id = b.id LIMIT 1) as dest_name
    FROM bookings b
    WHERE b.user_id = {$user['id']}
    ORDER BY b.booking_date DESC
";
$result = $conn->query($query);
$bookings = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);

function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return ['label' => 'Pay Now', 'class' => 'bg-yellow-100 text-yellow-700'];
        case 'waiting_payment': return ['label' => 'Verifying', 'class' => 'bg-orange-100 text-orange-700'];
        case 'paid': return ['label' => 'Paid', 'class' => 'bg-blue-100 text-blue-700'];
        case 'confirmed': return ['label' => 'Confirmed', 'class' => 'bg-indigo-100 text-indigo-700'];
        case 'completed': return ['label' => 'Completed', 'class' => 'bg-green-100 text-green-700'];
        case 'cancelled': return ['label' => 'Cancelled', 'class' => 'bg-red-100 text-red-700'];
        default: return ['label' => $status, 'class' => 'bg-gray-100 text-gray-700'];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Explore Kaltim</title>
    
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
                        }
                    },
                    fontFamily: {
                        'heading': ['Montserrat', 'sans-serif'],
                        'body': ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="font-body antialiased bg-gray-50">
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <a href="dashboard.php" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i class="fas fa-arrow-left text-gray-500"></i>
                    </a>
                    <h1 class="text-lg font-heading font-bold text-gray-900">Pesanan Saya</h1>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="container mx-auto px-4 lg:px-8 max-w-4xl">
            
            <div class="space-y-6">
                <?php if (empty($bookings)): ?>
                    <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clipboard-list text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 mb-8">Kamu belum memiliki riwayat pemesanan wisata.</p>
                        <a href="../explorasi.php" class="px-8 py-3 bg-primary-800 text-white font-bold rounded-2xl hover:bg-primary-700 transition shadow-lg shadow-primary-800/20">
                            Cari Destinasi Sekarang
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <?php $status = getStatusBadge($b['status']); ?>
                        <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 overflow-hidden">
                            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-lg uppercase tracking-widest"><?php echo $b['booking_code']; ?></span>
                                        <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($b['booking_date'])); ?></span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-heading font-bold text-gray-900 group-hover:text-primary-700 transition"><?php echo $b['dest_name']; ?></h3>
                                        <p class="text-sm text-gray-500 mt-1"><?php echo $b['package_name']; ?></p>
                                    </div>
                                </div>

                                <div class="flex flex-row md:flex-col items-center md:items-end justify-between gap-4 py-4 md:py-0 border-t md:border-t-0 border-gray-50">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 text-right mb-1">Total Pembayaran</p>
                                        <p class="text-lg font-extrabold text-gray-900">Rp <?php echo number_format($b['total_amount'], 0, ',', '.'); ?></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest <?php echo $status['class']; ?>">
                                            <?php echo $status['label']; ?>
                                        </span>
                                        <a href="booking_detail.php?id=<?php echo $b['id']; ?>" class="w-10 h-10 bg-gray-50 text-gray-400 hover:bg-primary-800 hover:text-white rounded-xl flex items-center justify-center transition-all duration-300">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>
</html>
