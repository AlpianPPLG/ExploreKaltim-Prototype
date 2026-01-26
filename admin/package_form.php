<?php
/**
 * ========================================
 * PACKAGE FORM (Admin)
 * ========================================
 * Add or Edit Package
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Check if editing
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $packageId > 0;

// Get package data if editing
$package = null;
if ($isEdit) {
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
    
    if (!$package) {
        header("Location: packages.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destinationId = (int)$_POST['destination_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $duration = trim($_POST['duration']);
    $stock = $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $errors = [];
    
    // Validation
    if (empty($name)) {
        $errors[] = "Package name is required";
    }
    
    if ($destinationId <= 0) {
        $errors[] = "Please select a destination";
    }
    
    if ($price < 0) {
        $errors[] = "Price must be greater than or equal to 0";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (empty($errors)) {
        if ($isEdit) {
            // Update existing package
            $stmt = $conn->prepare("UPDATE packages SET destination_id = ?, name = ?, description = ?, price = ?, duration = ?, stock = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("issdisii", $destinationId, $name, $description, $price, $duration, $stock, $isActive, $packageId);
        } else {
            // Insert new package
            $stmt = $conn->prepare("INSERT INTO packages (destination_id, name, description, price, duration, stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdisi", $destinationId, $name, $description, $price, $duration, $stock, $isActive);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = $isEdit ? "Package updated successfully!" : "Package created successfully!";
            $stmt->close();
            closeDBConnection($conn);
            header("Location: packages.php");
            exit();
        } else {
            $errors[] = "Failed to save package: " . $conn->error;
        }
        $stmt->close();
    }
}

// Get all destinations
$destinations = $conn->query("SELECT id, name FROM destinations ORDER BY name")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Package - Explore Kaltim Admin</title>
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
                    <a href="packages.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-display font-bold text-primary-800"><?php echo $isEdit ? 'Edit' : 'Add'; ?> Package</h1>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" class="space-y-6">
                <!-- Destination -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Destination *</label>
                    <select name="destination_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="0">Select Destination</option>
                        <?php foreach ($destinations as $dest): ?>
                            <option value="<?php echo $dest['id']; ?>" <?php echo ($package && $package['destination_id'] == $dest['id']) ? 'selected' : ''; ?>>
                                <?php echo escapeOutput($dest['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Package Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Package Name *</label>
                    <input type="text" name="name" required maxlength="255" value="<?php echo $package ? escapeOutput($package['name']) : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g. Paket Snorkeling Full Day">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description *</label>
                    <textarea name="description" required rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Describe what's included in this package..."><?php echo $package ? escapeOutput($package['description']) : ''; ?></textarea>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (Rp) *</label>
                    <input type="number" name="price" required min="0" step="0.01" value="<?php echo $package ? $package['price'] : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0">
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                    <input type="text" name="duration" maxlength="100" value="<?php echo $package ? escapeOutput($package['duration']) : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g. 2 Hari 1 Malam">
                    <p class="text-xs text-gray-500 mt-1">Optional. Example: "2 Hari 1 Malam" or "Full Day"</p>
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                    <input type="number" name="stock" min="0" value="<?php echo $package && $package['stock'] !== null ? $package['stock'] : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Leave empty for unlimited">
                    <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited stock</p>
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" <?php echo (!$package || $package['is_active']) ? 'checked' : ''; ?> class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active (visible to users)</label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 pt-4">
                    <a href="packages.php" class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 font-semibold transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 font-semibold transition">
                        <?php echo $isEdit ? 'Update' : 'Create'; ?> Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
