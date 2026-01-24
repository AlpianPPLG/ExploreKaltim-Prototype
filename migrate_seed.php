<?php
/**
 * ========================================
 * DATABASE MIGRATION SCRIPT
 * ========================================
 * Run this file to insert new seed data (Destinations & Galleries)
 * Access: http://localhost/ExploreKaltim/migrate_seed.php
 */

require_once 'config/database.php';

$conn = getDBConnection();

echo "<h2>Explore Kaltim - Data Migration</h2>";
echo "<pre>";

// 1. Ensure Categories and Regencies exist
echo "Checking Categories & Regencies...\n";

$categories = [
    ['Wisata Alam', 'nature', 'fas fa-tree'],
    ['Wisata Bahari', 'beach', 'fas fa-water'],
    ['Wisata Budaya', 'culture', 'fas fa-landmark'],
    ['Wisata Kuliner', 'culinary', 'fas fa-utensils']
];

foreach ($categories as $cat) {
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (name, slug, icon_class) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $cat[0], $cat[1], $cat[2]);
    $stmt->execute();
    $stmt->close();
}
echo "✓ Categories synchronized\n";

$regencies = [
    ['Samarinda', 'Ibu kota provinsi dengan pesona Sungai Mahakam'],
    ['Balikpapan', 'Kota minyak yang bersih dan modern dengan pantai indah'],
    ['Berau', 'Gerbang menuju surga bawah laut Derawan'],
    ['Kutai Kartanegara', 'Pusat sejarah kerajaan tertua di Indonesia']
];

foreach ($regencies as $reg) {
    $stmt = $conn->prepare("INSERT IGNORE INTO regencies (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $reg[0], $reg[1]);
    $stmt->execute();
    $stmt->close();
}
echo "✓ Regencies synchronized\n\n";

// 2. Insert Destinations
echo "Migrating Destinations...\n";

// Clear existing to avoid unique constraint errors during seed
// Note: In real migrations we'd be more careful, but for this prototype it's fine
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE destination_galleries");
$conn->query("TRUNCATE TABLE destinations");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$destinations = [
    [3, 1, 'Pulau Derawan', 'pulau-derawan', 'Surga bawah laut dengan penyu hijau dan terumbu karang yang spektakuler.', 'Kepulauan Derawan, Berau', '2.2519, 118.2436', '24 Jam', 0.00, 1],
    [3, 1, 'Danau Kakaban', 'danau-kakaban', 'Danau purba dengan ubur-ubur yang tidak menyengat.', 'Pulau Kakaban, Berau', '2.1481, 118.5306', '08:00 - 17:00', 25000.00, 1],
    [1, 4, 'Kampung Ketupat', 'kampung-ketupat', 'Destinasi kuliner dan wisata sungai di tepian Mahakam.', 'Jl. Slamet Riyadi, Samarinda', '-0.5021, 117.1536', '10:00 - 22:00', 5000.00, 0],
    [4, 3, 'Museum Mulawarman', 'museum-mulawarman', 'Bekas istana Kesultanan Kutai Kartanegara yang menyimpan sejarah panjang.', 'Tenggarong, Kutai Kartanegara', '-0.4147, 116.9911', '09:00 - 16:00', 10000.00, 1]
];

$destStmt = $conn->prepare("INSERT INTO destinations (regency_id, category_id, name, slug, description, address, map_coordinates, operating_hours, ticket_price, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$galleries = [
    'pulau-derawan' => 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&w=800&q=80',
    'danau-kakaban' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?auto=format&fit=crop&w=800&q=80',
    'kampung-ketupat' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
    'museum-mulawarman' => 'https://images.unsplash.com/photo-1596422846543-b5c64483f939?auto=format&fit=crop&w=800&q=80'
];

foreach ($destinations as $d) {
    $destStmt->bind_param("iissssssdi", $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], $d[7], $d[8], $d[9]);
    if ($destStmt->execute()) {
        $id = $conn->insert_id;
        echo "✓ Destination '{$d[2]}' added\n";
        
        // Add Gallery
        if (isset($galleries[$d[3]])) {
            $imgUrl = $galleries[$d[3]];
            $caption = "Keindahan " . $d[2];
            $isPrimary = 1;
            $galStmt = $conn->prepare("INSERT INTO destination_galleries (destination_id, image_url, caption, is_primary) VALUES (?, ?, ?, ?)");
            $galStmt->bind_param("issi", $id, $imgUrl, $caption, $isPrimary);
            $galStmt->execute();
            $galStmt->close();
            echo "  └ ✓ Thumbnail assigned\n";
        }
    } else {
        echo "✗ Error adding '{$d[2]}': " . $conn->error . "\n";
    }
}

$destStmt->close();

// 3. Insert Packages
echo "\nMigrating Packages...\n";
$conn->query("TRUNCATE TABLE packages");

$packages = [
    // Pulau Derawan (ID 1 if starting fresh)
    ['Paket Snorkeling Full Day', 'Snorkeling di 4 pulau (Derawan, Sangalaki, Kakaban, Maratua) termasuk alat dan makan siang.', 750000.00, 20, 1],
    ['Paket Honeymoon 3D2N', 'Menginap di water villa, dinner romantis, dan island hopping.', 4500000.00, 5, 1],
    // Danau Kakaban
    ['Tiket Masuk & Sewa Alat', 'Akses ke danau dan sewa fin/masker.', 50000.00, NULL, 1],
    // Kampung Ketupat
    ['Paket Makan Keluarga (4 Orang)', 'Menu ketupat lengkap dengan lauk pauk khas Banjar/Kaltim.', 250000.00, 10, 1],
    // Museum Mulawarman
    ['Tiket Dewasa', 'Akses masuk area museum dan keraton.', 10000.00, NULL, 1],
    ['Jasa Guide Sejarah', 'Pemandu profesional untuk menjelaskan sejarah Kesultanan Kutai.', 50000.00, 5, 1]
];

// We need to match packages to current destination IDs
$packageStmt = $conn->prepare("INSERT INTO packages (destination_id, name, description, price, stock, is_active) VALUES (?, ?, ?, ?, ?, ?)");

// Get destination IDs from slugs
$destMap = [];
$res = $conn->query("SELECT id, slug FROM destinations");
while ($row = $res->fetch_assoc()) { $destMap[$row['slug']] = $row['id']; }

$packageData = [
    'pulau-derawan' => [['Paket Snorkeling Full Day', 'Snorkeling di 4 pulau (Derawan, Sangalaki, Kakaban, Maratua) termasuk alat dan makan siang.', 750000.00, 20, 1], ['Paket Honeymoon 3D2N', 'Menginap di water villa, dinner romantis, dan island hopping.', 4500000.00, 5, 1]],
    'danau-kakaban' => [['Tiket Masuk & Sewa Alat', 'Akses ke danau dan sewa fin/masker.', 50000.00, NULL, 1]],
    'kampung-ketupat' => [['Paket Makan Keluarga (4 Orang)', 'Menu ketupat lengkap dengan lauk pauk khas Banjar/Kaltim.', 250000.00, 10, 1]],
    'museum-mulawarman' => [['Tiket Dewasa', 'Akses masuk area museum dan keraton.', 10000.00, NULL, 1], ['Jasa Guide Sejarah', 'Pemandu profesional untuk menjelaskan sejarah Kesultanan Kutai.', 50000.00, 5, 1]]
];

foreach ($packageData as $slug => $pkgs) {
    if (isset($destMap[$slug])) {
        $destId = $destMap[$slug];
        foreach ($pkgs as $p) {
            $packageStmt->bind_param("issidi", $destId, $p[0], $p[1], $p[2], $p[3], $p[4]);
            $packageStmt->execute();
        }
        echo "✓ Packages for '$slug' added\n";
    }
}
$packageStmt->close();

closeDBConnection($conn);

echo "\n========================================\n";
echo "Migration completed successfully!\n";
echo "========================================\n";
echo "</pre>";
?>
