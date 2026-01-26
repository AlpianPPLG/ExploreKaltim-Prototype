<?php
/**
 * ========================================
 * DATABASE MIGRATION RUNNER
 * ========================================
 * Run this file to apply all pending migrations
 * Access: http://localhost/ExploreKaltim/run_migrations.php
 */

require_once 'config/database.php';

$conn = getDBConnection();

echo "<h2>Explore Kaltim - Database Migrations</h2>";
echo "<pre>";
echo "========================================\n";
echo "Starting migrations...\n";
echo "========================================\n\n";

// Get all migration files
$migrationDir = __DIR__ . '/migrations';
$migrationFiles = glob($migrationDir . '/*.sql');
sort($migrationFiles);

if (empty($migrationFiles)) {
    echo "No migration files found.\n";
} else {
    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        echo "Running migration: $filename\n";
        echo "----------------------------------------\n";
        
        $sql = file_get_contents($file);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            if ($conn->multi_query($statement . ';')) {
                do {
                    if ($result = $conn->store_result()) {
                        while ($row = $result->fetch_assoc()) {
                            if (isset($row['status'])) {
                                echo "✓ " . $row['status'] . "\n";
                            }
                        }
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
            } else {
                // Check if error is about column already existing (which is OK)
                if (strpos($conn->error, 'Duplicate column name') === false && 
                    strpos($conn->error, 'already exists') === false) {
                    echo "✗ Error: " . $conn->error . "\n";
                }
            }
        }
        
        echo "✓ Migration $filename completed\n\n";
    }
}

closeDBConnection($conn);

echo "========================================\n";
echo "All migrations completed!\n";
echo "========================================\n";
echo "</pre>";
?>
