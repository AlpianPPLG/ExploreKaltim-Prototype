<?php
/**
 * ========================================
 * EXPLORASI DESTINASI
 * ========================================
 * Public exploration page for destinations
 */

require_once 'config/database.php';
session_start();
require_once 'config/security.php';

$conn = getDBConnection();

// Filters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 1; // Default to nature (Wisata Alam)
$regency_id = isset($_GET['regency']) ? (int)$_GET['regency'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get all categories and regencies for filters
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$regencies = $conn->query("SELECT * FROM regencies ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Build query
$query = "
    SELECT d.*, r.name as regency_name, c.name as category_name,
           (SELECT image_url FROM destination_galleries WHERE destination_id = d.id AND is_primary = 1 LIMIT 1) as thumbnail
    FROM destinations d
    JOIN regencies r ON d.regency_id = r.id
    JOIN categories c ON d.category_id = c.id
    WHERE 1=1
";

if ($category_id > 0) {
    $query .= " AND d.category_id = $category_id";
}
if ($regency_id > 0) {
    $query .= " AND d.regency_id = $regency_id";
}
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND d.name LIKE '%$search%'";
}

$query .= " ORDER BY d.is_featured DESC, d.created_at DESC";

$result = $conn->query($query);
$destinations = $result->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Wisata - Explore Kaltim</title>
    
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
                        'display': ['Montserrat', 'sans-serif'],
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

    <!-- Sub Header / Breadcrumb -->
    <div class="pt-24 pb-12 bg-primary-800 text-white">
        <div class="container mx-auto px-4 lg:px-8">
            <h1 class="text-4xl lg:text-5xl font-heading font-extrabold mb-4">Jelajahi Kalimantan Timur</h1>
            <p class="text-primary-100 text-lg max-w-2xl">Temukan keajaiban alam, budaya, dan cita rasa yang tersembunyi di Pulau Borneo.</p>
        </div>
    </div>

    <!-- Exploration Main Section -->
    <main class="container mx-auto px-4 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4 space-y-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-heading font-bold text-gray-900 text-lg mb-6 border-b pb-4">Filter Wisata</h3>
                    
                    <form action="explorasi.php" method="GET" class="space-y-6">
                        <!-- Search Box -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Cari Nama</label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo escapeOutput($search); ?>" 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition"
                                       placeholder="Misal: Derawan">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Kategori</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer group">
                                    <input type="radio" name="category" value="0" <?php echo $category_id == 0 ? 'checked' : ''; ?> class="w-4 h-4 text-primary-600">
                                    <span class="text-sm text-gray-600 group-hover:text-primary-800 transition">Semua Kategori</span>
                                </label>
                                <?php foreach ($categories as $cat): ?>
                                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer group">
                                        <input type="radio" name="category" value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'checked' : ''; ?> class="w-4 h-4 text-primary-600">
                                        <i class="<?php echo $cat['icon_class']; ?> w-5 text-gray-400 group-hover:text-primary-600 transition"></i>
                                        <span class="text-sm text-gray-600 group-hover:text-primary-800 transition"><?php echo $cat['name']; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Regency Filter -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Lokasi (Kabupaten/Kota)</label>
                            <select name="regency" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 transition text-sm">
                                <option value="0">Semua Lokasi</option>
                                <?php foreach ($regencies as $reg): ?>
                                    <option value="<?php echo $reg['id']; ?>" <?php echo $regency_id == $reg['id'] ? 'selected' : ''; ?>>
                                        <?php echo $reg['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary-800 text-white font-bold rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-800/20">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Results Grid -->
            <section class="w-full lg:w-3/4">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-gray-600">
                        Menampilkan <span class="font-bold text-gray-900"><?php echo count($destinations); ?></span> destinasi ditemukan
                    </p>
                </div>

                <?php if (empty($destinations)): ?>
                    <div class="bg-white p-12 rounded-2xl text-center border-2 border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-map-marker-alt text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">Destinasi Belum Tersedia</h3>
                        <p class="text-gray-500">Coba ubah filter pencarian Anda atau kembali lagi nanti.</p>
                        <a href="explorasi.php" class="inline-block mt-6 text-primary-600 font-bold hover:underline underline-offset-4">Reset Filter</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php foreach ($destinations as $dest): ?>
                            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-gray-100">
                                <!-- Image Header -->
                                <div class="relative h-64 overflow-hidden">
                                    <img src="<?php echo $dest['thumbnail'] ?: 'src/assets/images/placeholder.jpg'; ?>" 
                                         alt="<?php echo $dest['name']; ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    
                                    <!-- Category Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary-800 text-xs font-bold rounded-full shadow-sm">
                                            <?php echo $dest['category_name']; ?>
                                        </span>
                                    </div>

                                    <?php if ($dest['is_featured']): ?>
                                        <div class="absolute top-4 right-4">
                                            <span class="px-3 py-1 bg-secondary-500 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                                                <i class="fas fa-star"></i> Populer
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="p-6">
                                    <div class="flex items-center gap-2 text-xs font-medium text-gray-400 mb-2">
                                        <i class="fas fa-location-dot text-primary-500"></i>
                                        <?php echo $dest['regency_name']; ?>
                                    </div>
                                    <h3 class="text-xl font-heading font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition">
                                        <?php echo $dest['name']; ?>
                                    </h3>
                                    <p class="text-gray-500 text-sm line-clamp-2 mb-6">
                                        <?php echo $dest['description']; ?>
                                    </p>

                                    <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Mulai Dari</p>
                                            <p class="text-lg font-bold text-primary-800">
                                                Rp <?php echo number_format($dest['ticket_price'], 0, ',', '.'); ?>
                                            </p>
                                        </div>
                                        <a href="detail.php?slug=<?php echo $dest['slug']; ?>" 
                                           class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 text-gray-600 group-hover:bg-primary-800 group-hover:text-white rounded-full transition-all duration-300">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <!-- Footer Simulation -->
    <footer class="bg-white border-t border-gray-200 mt-20 py-12">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <p class="text-gray-400 text-sm">&copy; 2024 Explore Kaltim. All rights reserved.</p>
        </div>
    </footer>

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
                    
                    // Simple animation for hamburger
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
            }
        });
    </script>
</body>
</html>
