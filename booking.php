<?php
/**
 * ========================================
 * BOOKING FORM (Checkout)
 * ========================================
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/security.php';

// Require login to book
requireLogin('login.php');

$conn = getDBConnection();
$user = getCurrentUser();

$packageId = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;

if ($packageId === 0) {
    header("Location: explorasi.php");
    exit();
}

// Get Package & Destination Info
$query = "
    SELECT p.*, d.name as dest_name, d.slug as dest_slug, r.name as regency_name,
           (SELECT image_url FROM destination_galleries WHERE destination_id = d.id AND is_primary = 1 LIMIT 1) as thumbnail
    FROM packages p
    JOIN destinations d ON p.destination_id = d.id
    JOIN regencies r ON d.regency_id = r.id
    WHERE p.id = $packageId AND p.is_active = 1
";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: explorasi.php");
    exit();
}

$pkg = $result->fetch_assoc();

// Handle Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $travelDate = $_POST['travel_date'];
    $quantity = (int)$_POST['quantity'];
    $note = trim($_POST['note']);
    
    // Validation
    $errors = [];
    
    // Validate travel date (must be today or future)
    $today = date('Y-m-d');
    if ($travelDate < $today) {
        $errors[] = "Tanggal perjalanan tidak boleh di masa lalu.";
    }
    
    // Validate quantity
    if ($quantity < 1) {
        $errors[] = "Jumlah peserta minimal 1 orang.";
    }
    
    if (empty($errors)) {
        $totalAmount = $pkg['price'] * $quantity;
        $bookingCode = "INV-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -5));
        
        // 1. Insert into bookings
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, booking_code, total_amount, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("isd", $user['id'], $bookingCode, $totalAmount);
        
        if ($stmt->execute()) {
            $bookingId = $conn->insert_id;
            
            // 2. Insert into booking_details
            $detailStmt = $conn->prepare("INSERT INTO booking_details (booking_id, package_id, quantity, price_per_unit, subtotal, travel_date, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $detailStmt->bind_param("iiiddss", $bookingId, $packageId, $quantity, $pkg['price'], $totalAmount, $travelDate, $note);
            
            if ($detailStmt->execute()) {
                $_SESSION['booking_success'] = "Good news! Pesanan Kamu berhasil dibuat. Silakan lakukan pembayaran.";
                header("Location: user/booking_detail.php?id=" . $bookingId);
                exit();
            } else {
                $errors[] = "Gagal menyimpan detail booking. Silakan coba lagi.";
            }
        } else {
            $errors[] = "Gagal membuat pesanan. Silakan coba lagi.";
        }
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Explore Kaltim</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="src/css/style.css">
    
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
                        'body': ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="font-body antialiased bg-gray-50">
    <!-- Navbar -->
    <?php require_once 'navbar.php'; ?>

    <main class="pt-32 pb-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12 max-w-6xl mx-auto">
                
                <!-- Left: Form Section -->
                <div class="w-full lg:w-3/5">
                    <div class="mb-10">
                        <h1 class="text-3xl font-heading font-extrabold text-gray-900 mb-2">Checkout</h1>
                        <p class="text-gray-500">Lengkapi detail perjalanan Anda di bawah ini.</p>
                    </div>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="mb-8 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl">
                            <ul class="list-disc list-inside space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-8">
                        <!-- Step 1: Schedule -->
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-heading font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-sm">1</span>
                                Jadwal Kunjungan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Perjalanan</label>
                                    <input type="date" name="travel_date" required min="<?php echo date('Y-m-d'); ?>"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jumlah Peserta (Pax)</label>
                                    <input type="number" name="quantity" required min="1" value="1" id="qty-input"
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Personal Info -->
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-heading font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-sm">2</span>
                                Catatan Tambahan
                            </h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pesan Khusus (Opsional)</label>
                                <textarea name="note" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition"
                                          placeholder="Misal: Saya alergi seafood atau butuh penjemputan..."></textarea>
                            </div>
                        </div>

                        <!-- Submission -->
                        <div class="flex items-center gap-4">
                            <button type="submit" class="flex-1 py-4 bg-primary-800 text-white font-bold rounded-2xl hover:bg-primary-700 transition shadow-xl shadow-primary-800/20 text-lg">
                                Konfirmasi Booking
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Summary Section -->
                <div class="w-full lg:w-2/5">
                    <div class="sticky top-32 space-y-6">
                        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                            <!-- Image -->
                            <div class="h-48 overflow-hidden">
                                <img src="<?php echo $pkg['thumbnail']; ?>" class="w-full h-full object-cover">
                            </div>
                            
                            <!-- Content -->
                            <div class="p-8">
                                <div class="mb-6">
                                    <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest mb-1"><?php echo $pkg['dest_name']; ?></p>
                                    <h4 class="text-xl font-heading font-bold text-gray-900"><?php echo $pkg['name']; ?></h4>
                                    <p class="text-xs text-gray-400 mt-1"><i class="fas fa-location-dot mr-1"></i> <?php echo $pkg['regency_name']; ?></p>
                                </div>

                                <div class="space-y-4 pt-6 border-t border-gray-100">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Harga Satuan</span>
                                        <span class="font-bold text-gray-900">Rp <?php echo number_format($pkg['price'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Kuantitas</span>
                                        <span class="font-bold text-gray-900" id="summary-qty">1 Pax</span>
                                    </div>
                                    <div class="flex justify-between text-lg pt-4 border-t border-gray-100">
                                        <span class="font-heading font-bold text-gray-900">Total Pembayaran</span>
                                        <span class="font-heading font-extrabold text-primary-800" id="summary-total">
                                            Rp <?php echo number_format($pkg['price'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Info -->
                            <div class="px-8 py-5 bg-gray-50 flex items-center gap-3">
                                <i class="fas fa-check-circle text-primary-500"></i>
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Konfirmasi Instan</span>
                            </div>
                        </div>

                        <!-- Policy Card -->
                        <div class="p-6 bg-white rounded-3xl border border-gray-100 text-xs text-gray-400 leading-relaxed italic">
                            Dengan menekan tombol Konfirmasi, Anda menyetujui seluruh Syarat & Ketentuan yang berlaku di Explore Kaltim.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Simple reactive UI for checkout summary
        const qtyInput = document.getElementById('qty-input');
        const summaryQty = document.getElementById('summary-qty');
        const summaryTotal = document.getElementById('summary-total');
        const basePrice = <?php echo $pkg['price']; ?>;

        qtyInput.addEventListener('input', function() {
            let qty = parseInt(this.value) || 0;
            if(qty < 1) qty = 1;
            
            summaryQty.textContent = qty + ' Pax';
            
            let total = qty * basePrice;
            summaryTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
        
        // Form validation
        const bookingForm = document.querySelector('form');
        const travelDateInput = document.querySelector('input[name="travel_date"]');
        
        bookingForm.addEventListener('submit', function(e) {
            const travelDate = new Date(travelDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (travelDate < today) {
                e.preventDefault();
                alert('Tanggal perjalanan tidak boleh di masa lalu!');
                return false;
            }
            
            const qty = parseInt(qtyInput.value);
            if (qty < 1) {
                e.preventDefault();
                alert('Jumlah peserta minimal 1 orang!');
                return false;
            }
            
            return true;
        });
    </script>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuBtn && mobileMenu) {
                let isOpen = false;
                const lines = menuBtn.querySelectorAll('.hamburger-line');
                
                menuBtn.addEventListener('click', function() {
                    isOpen = !isOpen;
                    mobileMenu.classList.toggle('open', isOpen);
                    document.body.style.overflow = isOpen ? 'hidden' : '';
                    
                    if (isOpen) {
                        lines[0].style.transform = 'translateY(8px) rotate(45deg)';
                        lines[1].style.opacity = '0';
                        lines[2].style.transform = 'translateY(-8px) rotate(-45deg)';
                    } else {
                        lines[0].style.transform = '';
                        lines[1].style.opacity = '1';
                        lines[2].style.transform = '';
                    }
                });
                
                const menuLinks = mobileMenu.querySelectorAll('[data-close-menu]');
                menuLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        isOpen = false;
                        mobileMenu.classList.remove('open');
                        document.body.style.overflow = '';
                        lines[0].style.transform = '';
                        lines[1].style.opacity = '1';
                        lines[2].style.transform = '';
                    });
                });
            }
        });
    </script>
</body>
</html>
