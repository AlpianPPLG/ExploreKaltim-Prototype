<?php
/**
 * ========================================
 * DETAIL DESTINASI
 * ========================================
 */

require_once 'config/database.php';
session_start();
require_once 'config/security.php';

$conn = getDBConnection();

$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

if (empty($slug)) {
    header("Location: explorasi.php");
    exit();
}

// Get Destination data
$query = "
    SELECT d.*, r.name as regency_name, c.name as category_name 
    FROM destinations d
    JOIN regencies r ON d.regency_id = r.id
    JOIN categories c ON d.category_id = c.id
    WHERE d.slug = '$slug'
    LIMIT 1
";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: explorasi.php");
    exit();
}

$dest = $result->fetch_assoc();
$destId = $dest['id'];

// Get Gallery
$galleryRes = $conn->query("SELECT * FROM destination_galleries WHERE destination_id = $destId ORDER BY is_primary DESC");
$gallery = $galleryRes->fetch_all(MYSQLI_ASSOC);

// Get Packages
$packageRes = $conn->query("SELECT * FROM packages WHERE destination_id = $destId AND is_active = 1 ORDER BY price ASC");
$packages = $packageRes->fetch_all(MYSQLI_ASSOC);

// Get Reviews
$reviewQuery = "
    SELECT r.*, u.username, u.avatar_url 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.destination_id = $destId
    ORDER BY r.created_at DESC
";
$reviewRes = $conn->query($reviewQuery);
$reviews = $reviewRes->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $dest['name']; ?> - Explore Kaltim</title>
    
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

    <!-- Image Header / Gallery Section -->
    <div class="pt-16">
        <div class="relative h-[50vh] lg:h-[70vh] w-full overflow-hidden bg-gray-900">
            <img src="<?php echo $gallery[0]['image_url'] ?? 'src/assets/images/placeholder.jpg'; ?>" 
                 alt="<?php echo $dest['name']; ?>"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 w-full p-8 lg:p-16">
                <div class="container mx-auto">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-primary-600 text-white text-xs font-bold rounded-full">
                            <?php echo $dest['category_name']; ?>
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-full border border-white/20">
                            <i class="fas fa-location-dot mr-1"></i> <?php echo $dest['regency_name']; ?>
                        </span>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-heading font-extrabold text-white mb-4">
                        <?php echo $dest['name']; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column: Content -->
            <div class="w-full lg:w-2/3 space-y-12">
                <!-- Description -->
                <section>
                    <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info text-primary-600 text-sm"></i>
                        </span>
                        Tentang Destinasi
                    </h2>
                    <div class="prose prose-lg text-gray-600 leading-relaxed">
                        <?php echo nl2br(escapeOutput($dest['description'])); ?>
                    </div>
                </section>

                <!-- Information Grid -->
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-primary-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Jam Operasional</p>
                            <p class="font-semibold text-gray-900"><?php echo $dest['operating_hours'] ?: 'Setiap Hari'; ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-primary-600">
                            <i class="fas fa-ticket text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Harga Tiket Masuk</p>
                            <p class="font-semibold text-gray-900">
                                <?php echo $dest['ticket_price'] > 0 ? 'Mulai Rp '.number_format($dest['ticket_price'], 0, ',', '.') : 'Gratis / Menyesuaikan'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 md:col-span-2">
                        <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-primary-600 shrink-0">
                            <i class="fas fa-map-location-dot text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                            <p class="font-semibold text-gray-900 leading-relaxed"><?php echo escapeOutput($dest['address']); ?></p>
                            <?php if ($dest['map_coordinates']): ?>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($dest['map_coordinates']); ?>" target="_blank"
                                   class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-primary-600 hover:text-primary-700">
                                    Lihat di Google Maps <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Reviews -->
                <section>
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-heading font-bold text-gray-900 flex items-center gap-3">
                            <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-yellow-600 text-sm"></i>
                            </span>
                            Ulasan Turis
                        </h2>
                        <span class="text-sm font-bold text-gray-400"><?php echo count($reviews); ?> ulasan</span>
                    </div>

                    <div class="space-y-6">
                        <?php if (empty($reviews)): ?>
                            <div class="text-center py-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                                <p class="text-gray-400 italic">Belum ada ulasan untuk destinasi ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center gap-4 mb-4">
                                        <img src="<?php echo $rev['avatar_url']; ?>" class="w-12 h-12 rounded-full border-2 border-primary-100 p-0.5">
                                        <div>
                                            <h4 class="font-bold text-gray-900"><?php echo $rev['username']; ?></h4>
                                            <div class="flex text-yellow-400 text-xs gap-0.5 mt-0.5">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i class="<?php echo $i <= $rev['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <span class="ml-auto text-xs text-gray-400"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></span>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed">"<?php echo escapeOutput($rev['comment']); ?>"</p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Right Column: Booking & Packages -->
            <div class="w-full lg:w-1/3">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-primary-800 text-white rounded-3xl p-8 shadow-xl shadow-primary-800/20 overflow-hidden relative">
                        <div class="absolute top-0 right-0 -mr-12 -mt-12 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        
                        <h3 class="text-xl font-heading font-bold mb-6 relative">Pilih Paket Wisata</h3>
                        
                        <?php if (empty($packages)): ?>
                            <p class="text-primary-100 text-center py-8 italic border-2 border-dashed border-white/20 rounded-2xl">
                                Maaf, paket belum tersedia untuk destinasi ini.
                            </p>
                        <?php else: ?>
                            <div class="space-y-4 relative">
                                <?php foreach ($packages as $pkg): ?>
                                    <div class="group bg-white/10 p-5 rounded-2xl border border-white/10 hover:bg-white/20 transition cursor-pointer">
                                        <div class="flex flex-col mb-3">
                                            <span class="font-bold text-white group-hover:text-secondary-400 transition"><?php echo $pkg['name']; ?></span>
                                            <span class="text-xs text-primary-100 mt-1 line-clamp-2"><?php echo $pkg['description']; ?></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-white">Rp <?php echo number_format($pkg['price'], 0, ',', '.'); ?></span>
                                            <a href="booking.php?package_id=<?php echo $pkg['id']; ?>" 
                                               class="px-4 py-2 bg-secondary-500 text-white text-xs font-bold rounded-xl hover:bg-secondary-400 transition shadow-lg shadow-secondary-500/30">
                                                Booking
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-8 pt-8 border-t border-white/10 text-center">
                            <p class="text-[10px] text-primary-200 uppercase font-bold tracking-[0.2em]">Partner Terpercaya</p>
                            <div class="flex justify-center gap-6 mt-4 opacity-50 grayscale invert">
                                <i class="fab fa-cc-visa text-2xl"></i>
                                <i class="fab fa-cc-mastercard text-2xl"></i>
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Side Info -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center">
                        <i class="fas fa-shield-halved text-3xl text-primary-500 mb-4"></i>
                        <h4 class="font-bold text-gray-900 mb-2">Garansi Keamanan</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">Sistem pembayaran terenkripsi dan verifikasi identitas untuk kenyamanan perjalanan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 mt-20 py-12">
        <div class="container mx-auto px-4 lg:px-8 text-center text-gray-400 text-sm">
            <p>&copy; 2024 Explore Kaltim. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
