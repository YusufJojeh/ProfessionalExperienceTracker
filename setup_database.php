<?php
// Database Setup Script
// Run this script to set up the database with all required tables and sample data

require_once 'config/database.php';

echo "Setting up Professional Experience Tracker Database...\n";

// Read and execute the schema file
$schema_file = 'database/schema.sql';
if (file_exists($schema_file)) {
    $sql = file_get_contents($schema_file);
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                if ($conn->query($statement)) {
                    echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
                } else {
                    echo "✗ Error executing: " . substr($statement, 0, 50) . "...\n";
                    echo "Error: " . $conn->error . "\n";
                }
            } catch (Exception $e) {
                echo "✗ Exception: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nDatabase setup completed successfully!\n";
    echo "You can now access the application at: http://localhost:8000\n";
    echo "\nDefault login credentials:\n";
    echo "Admin: admin@example.com / admin123\n";
    echo "User: ahmed@example.com / user123\n";
    
} else {
    echo "✗ Schema file not found: $schema_file\n";
}

$conn->close();
?>
