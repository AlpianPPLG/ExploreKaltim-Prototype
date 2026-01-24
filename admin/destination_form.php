<?php
/**
 * ========================================
 * DESTINATION FORM (Add/Edit)
 * ========================================
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/security.php';

// Require admin access
requireLogin('../login.php');
requireAdmin('../user/dashboard.php');

$conn = getDBConnection();

// Get categories and regencies for dropdowns
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$regencies = $conn->query("SELECT * FROM regencies ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$destination = null;
$isEdit = false;

// If editing, get current data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $destination = $result->fetch_assoc();
        $isEdit = true;
        
        // Get primary image
        $imgStmt = $conn->prepare("SELECT image_url FROM destination_galleries WHERE destination_id = ? AND is_primary = 1 LIMIT 1");
        $imgStmt->bind_param("i", $id);
        $imgStmt->execute();
        $imgRes = $imgStmt->get_result();
        $primaryImg = $imgRes->num_rows > 0 ? $imgRes->fetch_assoc()['image_url'] : '';
        $imgStmt->close();
    } else {
        $_SESSION['error_msg'] = "Destination not found.";
        header("Location: destinations.php");
        exit();
    }
    $stmt->close();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $regency_id = (int)$_POST['regency_id'];
    $category_id = (int)$_POST['category_id'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $map_coordinates = $_POST['map_coordinates'];
    $operating_hours = $_POST['operating_hours'];
    $ticket_price = (float)$_POST['ticket_price'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if ($isEdit) {
        $stmt = $conn->prepare("
            UPDATE destinations 
            SET name = ?, slug = ?, regency_id = ?, category_id = ?, description = ?, 
                address = ?, map_coordinates = ?, operating_hours = ?, ticket_price = ?, is_featured = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssiissssdii", $name, $slug, $regency_id, $category_id, $description, $address, $map_coordinates, $operating_hours, $ticket_price, $is_featured, $id);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO destinations (name, slug, regency_id, category_id, description, address, map_coordinates, operating_hours, ticket_price, is_featured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssiissssdi", $name, $slug, $regency_id, $category_id, $description, $address, $map_coordinates, $operating_hours, $ticket_price, $is_featured);
    }

    if ($stmt->execute()) {
        $destId = $isEdit ? $id : $conn->insert_id;
        $imageUrl = $_POST['image_url'];
        
        if (!empty($imageUrl)) {
            if ($isEdit) {
                // Update or Insert primary image
                $checkImg = $conn->query("SELECT id FROM destination_galleries WHERE destination_id = $destId AND is_primary = 1");
                if ($checkImg->num_rows > 0) {
                    $updImg = $conn->prepare("UPDATE destination_galleries SET image_url = ? WHERE destination_id = ? AND is_primary = 1");
                    $updImg->bind_param("si", $imageUrl, $destId);
                    $updImg->execute();
                    $updImg->close();
                } else {
                    $insImg = $conn->prepare("INSERT INTO destination_galleries (destination_id, image_url, is_primary) VALUES (?, ?, 1)");
                    $insImg->bind_param("is", $destId, $imageUrl);
                    $insImg->execute();
                    $insImg->close();
                }
            } else {
                $insImg = $conn->prepare("INSERT INTO destination_galleries (destination_id, image_url, is_primary) VALUES (?, ?, 1)");
                $insImg->bind_param("is", $destId, $imageUrl);
                $insImg->execute();
                $insImg->close();
            }
        }

        $_SESSION['success_msg'] = "Destination saved successfully.";
        header("Location: destinations.php");
        exit();
    } else {
        $error = "Error saving destination: " . $conn->error;
    }
    $stmt->close();
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Destination - Explore Kaltim Admin</title>
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
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="destinations.php" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h1 class="text-xl font-display font-bold text-primary-800">
                        <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Destination
                    </h1>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-6">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destination Name</label>
                        <input type="text" name="name" required value="<?php echo $isEdit ? escapeOutput($destination['name']) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g. Pulau Derawan">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Image URL</label>
                        <input type="url" name="image_url" value="<?php echo isset($primaryImg) ? escapeOutput($primaryImg) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                               placeholder="https://example.com/image.jpg">
                        <p class="mt-1 text-xs text-gray-500">Provide a direct link to a photo.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug (Optional)</label>
                        <input type="text" name="slug" value="<?php echo $isEdit ? escapeOutput($destination['slug']) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                               placeholder="pulau-derawan">
                        <p class="mt-1 text-xs text-gray-500">Auto-generated from name if left blank.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (Rp)</label>
                        <input type="number" name="ticket_price" value="<?php echo $isEdit ? (float)$destination['ticket_price'] : '0'; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Regency</label>
                        <select name="regency_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-medium">
                            <option value="">Select Regency</option>
                            <?php foreach ($regencies as $reg): ?>
                                <option value="<?php echo $reg['id']; ?>" <?php echo ($isEdit && $destination['regency_id'] == $reg['id']) ? 'selected' : ''; ?>>
                                    <?php echo escapeOutput($reg['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-medium">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($isEdit && $destination['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo escapeOutput($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detailed Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-display font-bold text-gray-900 mb-6">Details & Location</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
                        <textarea name="description" rows="5" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"><?php echo $isEdit ? escapeOutput($destination['description']) : ''; ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" rows="2" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"><?php echo $isEdit ? escapeOutput($destination['address']) : ''; ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operating Hours</label>
                            <input type="text" name="operating_hours" value="<?php echo $isEdit ? escapeOutput($destination['operating_hours']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g. 08:00 - 17:00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Map Coordinates</label>
                            <input type="text" name="map_coordinates" value="<?php echo $isEdit ? escapeOutput($destination['map_coordinates']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="-1.2345, 116.7890">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" <?php echo ($isEdit && $destination['is_featured']) ? 'checked' : ''; ?>
                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">Display this destination as "Featured" on home page</label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <a href="destinations.php" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-bold shadow-md">
                    <?php echo $isEdit ? 'Update' : 'Create'; ?> Destination
                </button>
            </div>
        </form>
    </div>
</body>
</html>
